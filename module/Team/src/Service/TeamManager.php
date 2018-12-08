<?php
/**
 * Created by PhpStorm.
 * User: ogismatulin@srs.lan
 * Date: 07.12.18
 * Time: 9:41
 */

namespace Team\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Team\Classes\PlayoffFinishedMatch;
use Team\Entity\MatchResult;
use Team\Entity\TeamMatch;
use Team\Entity\Team;
use Team\Entity\TeamSplit;
use Team\Entity\TournamentStatus;
use ZendDeveloperTools\Exception\ParameterMissingException;

class TeamManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * TeamManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getAllTeams()
    {
        return $this->getTeamRep()->findAll();
    }

    /**
     * Возвращаем матчи по типу
     *
     * @param int $typeId
     * @return TeamMatch[]
     */
    public function getMatchesByType(int $typeId)
    {
        return $this->getTeamMatchRep()->findAllByType($typeId);
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    public function saveTeam(Team $team): bool
    {
        $isSave = true;
        try {
            $this->em->persist($team);
            $this->em->flush();
        } catch (ORMException $e) {
            $isSave = false;
        }

        return $isSave;
    }

    /**
     * Можно делить команды только при определенном количестве команд
     *
     * @return bool
     */
    public function isCanSplit(): bool
    {
        $isCan = false;
        $count = $this->getTeamRep()->getCount();

        if ($count >= Team::TEAM_AMOUNT) {
            $isCan = true;
        }

        return $isCan;
    }

    /**
     * Делим по группам
     */
    public function splitByGroup(): bool
    {
        $success    = true;
        $teams      = $this->getAllTeams();
        $teamsSplit = $this->shuffleAssoc($teams);

        $len    = count($teamsSplit);
        $group1 = array_slice($teamsSplit, 0, $len / 2);
        $group2 = array_slice($teamsSplit, $len / 2);


        try {
            $this->updateTournamentStatus(TournamentStatus::STATUS_SPLIT_TEAM);
            $this->deleteTeamSplit();
            $this->createGroup($group1, TeamSplit::GROUP1);
            $this->createGroup($group2, TeamSplit::GROUP2);
        } catch (ORMException $e) {
            $success = false;
        }

        return $success;
    }

    /**
     * Перемешиваем массив в случайном порядке
     *
     * @param $array
     * @return array| Team[]
     */
    private function shuffleAssoc($array): array
    {
        $keys = array_keys($array);

        shuffle($keys);

        $new = array();
        foreach ($keys as $key) {
            $new[$key] = $array[$key];
        }

        return $new;
    }

    /**
     * Создание матча где сильная команда играет со слабой
     *
     * @param array|Team[] $teams
     * @return array|Team[]
     */
    private function strongWithWeakSort(array $teams)
    {
        usort($teams, function (Team $a, Team $b) {
            $score1 = explode(':', $a->result->getResult())[0];
            $score2 = explode(':', $b->result->getResult())[0];
            if ($score1 === $score2) {
                return 0;
            }

            return $score1 < $score2 ? 1 : -1;
        });

        return $teams;
    }

    /**
     * Добавляем команды в указанную группу
     *
     * @param Team[] $group
     * @param string $groupName
     * @throws ORMException
     */
    private function createGroup(array $group, string $groupName): void
    {
        foreach ($group as $team) {
            $teamSplit = new TeamSplit();
            $teamSplit->setTeam($team);
            $teamSplit->setGroupName($groupName);
            $this->em->persist($teamSplit);
        }

        $this->em->flush();
    }

    /**
     * Обновляем или создаем текущий статус турнира
     *
     * @param int $statusId
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function updateTournamentStatus(int $statusId)
    {
        $status = $this->getTournamentStatus();
        $status->setStatusId($statusId);
        $this->em->persist($status);
        $this->em->flush();
    }

    /**
     * Удаляем все записи из TeamSplit
     */
    private function deleteTeamSplit()
    {
        // Удаляем старую группу, если есть
        $this
            ->em->createQueryBuilder()
                ->delete(TeamSplit::class)
                ->getQuery()
                ->execute();

        $this->deleteMatchSplit();
        $this->deleteMatchResult();
    }

    /**
     * Удаляем все записи из match
     * @param int $typeId
     */
    private function deleteMatchSplit(int $typeId = null)
    {
        // Удаляем старую группу, если есть
        $qb = $this->em->createQueryBuilder();
        $qb->delete(TeamMatch::class, 'tm');

        if ($typeId && $typeId != TeamMatch::TYPE_QUALIFYING) {
            $qb->where($qb->expr()->eq('tm.typeId', $typeId));
        }
        $qb->getQuery()->execute();
    }


    /**
     * Удаляем все результаты матча или по типу(нужно для перегенерации)
     *
     * @param int $typeId
     * @throws \Doctrine\DBAL\DBALException
     */
    private function deleteMatchResult(int $typeId = null)
    {
        $where = '';
        if ($typeId && $typeId != TeamMatch::TYPE_QUALIFYING) {
            $where = 'WHERE m.type_id = ' . $typeId;
        }
        // Удаляем старую группу, если есть
        $this->em->getConnection()->executeQuery('
            DELETE r FROM match_result r
              JOIN team_match m ON m.id = r.match_id ' . $where
        );
    }

    public function getTournamentStatus()
    {
        $result = null;
        $status = $this->em->getRepository(TournamentStatus::class)->findAll();
        if (count($status) === 0) {
            $result = new TournamentStatus();
            $result->setStatusId(TournamentStatus::STATUS_CREATE_TEAM);
            $this->em->persist($result);
            $this->em->flush($result);
        } else {
            $result = current($status);
        }

        return $result;
    }

    /**
     * жеребьевка для отборочных
     *
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function splitByMatchQualifying()
    {
        $group1 = $this->getTeamRep()->findByGroup(TeamSplit::GROUP1);
        $group2 = $this->getTeamRep()->findByGroup(TeamSplit::GROUP2);


        $this->deleteMatchSplit(TeamMatch::TYPE_QUALIFYING);

        $this->createMatch($group1, TeamSplit::GROUP1, TeamMatch::TYPE_QUALIFYING);
        $this->createMatch($group2, TeamSplit::GROUP2, TeamMatch::TYPE_QUALIFYING);

        $this->em->flush();

        $this->updateTournamentStatus(TournamentStatus::STATUS_SPLIT_MATCH);

    }

    /**
     * жеребьевка для playoff
     *
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function splitByMatchPlayoff()
    {
        $typeId     = $this->getLastMatchTypeId();
        $group      = $this->getTeamRep()->findWin($typeId);
        $nextTypeId = $this->getNextMatchTypeId($typeId);

        if ($nextTypeId === null) {
            return false;
        }

        $this->deleteMatchSplit($nextTypeId);

        $this->createMatch($group, null, $nextTypeId);

        $this->em->flush();

        $this->updateTournamentStatus(TournamentStatus::STATUS_SPLIT_MATCH_PLAYOFF);

        return true;
    }

    public function getLastMatchTypeId()
    {
        return $this->getTeamRep()->getLastMatchTypeId();
    }

    /**
     * @return \Team\Entity\Repository\TeamRepository| \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    public function getTeamRep()
    {
        return $this->getEntityManager()->getRepository(Team::class);
    }

    /**
     * @param Team[] $teams
     * @param string $groupName
     * @param int    $typeId
     * @throws ORMException
     */
    private function createMatch(array $teams, string $groupName = null, int $typeId)
    {
        $len = count($teams);

        // Для первой игры в playoff делим сильные с слабыми
        if ($typeId === TeamMatch::TYPE_PLAYOFF_4) {
            $teamsSplit = $this->strongWithWeakSort($teams);
        } else {
            $teamsSplit = $this->shuffleAssoc($teams);
        }
        $half1 = array_slice($teamsSplit, 0, $len / 2);
        $half2 = array_slice($teamsSplit, $len / 2);

        for ($i = 0; $i < $len / 2; $i++) {
            $match = new TeamMatch();
            $match->setTeam1($half1[$i]);
            $match->setTeam2($half2[$i]);
            $match->setGroupName($groupName);
            $match->setTypeId($typeId);
            $this->em->persist($match);
        }
    }

    /**
     * Генерируем результаты для отборочных
     *
     * @param int $typeId
     * @return string
     * @throws ORMException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws ParameterMissingException
     */
    public function generateMatchResult(int $typeId)
    {
        if ($typeId === 0) {
            $typeId = $this->getLastMatchTypeId();
        }

        // Получаем последнии распределленные команды
        /** @var TeamMatch[] $matches */
        $matches = $this->getTeamMatchRep()->findAllByType($typeId);

        // Если нету записей значит команды не распределяли
        if(count($matches) === 0){
            // Распределяем
            $this->splitByMatchPlayoff();
            $matches = $this->getTeamMatchRep()->findAllByType($typeId);
        }

        $this->deleteMatchResult($typeId);

        foreach ($matches as $match) {
            // Генерируем случайный результат
            $r  = str_shuffle("0123456789");
            $r1 = (int)$r[0];
            $r2 = (int)$r[1];

            // Сохраняем результат
            $result = new MatchResult();
            $result
                ->setTeamWin($match->getTeam1())
                ->setMatch($match)
                // Самый большой результат записываем первым
                ->setResult($r1 > $r2 ? $r1 . ':' . $r2 : $r2 . ':' . $r1);
            $this->em->persist($result);
        }

        $this->em->flush();

        // Меняем статус турнира
        switch ($typeId) {
            case TeamMatch::TYPE_QUALIFYING:
                $status = TournamentStatus::STATUS_QUALIFYING;
                break;
            case TeamMatch::TYPE_PLAYOFF_4:
                $status = TournamentStatus::STATUS_PLAYOFF_4;
                break;
            case TeamMatch::TYPE_PLAYOFF_2:
                $status = TournamentStatus::STATUS_PLAYOFF_2;
                break;
            case TeamMatch::TYPE_PLAYOFF_1:
                $status = TournamentStatus::STATUS_PLAYOFF_1;
                break;
            default:
                throw new ParameterMissingException('Не известный тип');
        }
        if ($typeId === TeamMatch::TYPE_QUALIFYING) {
            $action = 'qualifying';
        } else {
            $action = 'playoff';
        }
        $this->updateTournamentStatus($status);

        return $action;
    }

    /**
     * @return \Team\Entity\Repository\TeamMatchRepository| \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    private function getTeamMatchRep()
    {
        return $this->em->getRepository(TeamMatch::class);
    }

    public function getNextMatchTypeId(int $typeId)
    {
        $nextTypeId = null;
        foreach (TeamMatch::TYPE_ORDER as $order => $id) {
            if ($id == $typeId && isset(TeamMatch::TYPE_ORDER[$order + 1])) {
                $nextTypeId = TeamMatch::TYPE_ORDER[$order + 1];
            }
        }

        return $nextTypeId;
    }

    /**
     * Удаляем все записи
     */
    public function clearDB()
    {
        $qb = $this->em->createQueryBuilder();

        $qb->delete(TeamSplit::class)->getQuery()->execute();
        $qb->delete(TeamMatch::class)->getQuery()->execute();
        $qb->delete(MatchResult::class)->getQuery()->execute();
        $qb->delete(TournamentStatus::class)->getQuery()->execute();
    }

    /**
     * Удаляем все записи
     */
    public function clearTeam()
    {
        $qb = $this->em->createQueryBuilder();

        $qb->delete(Team::class)->getQuery()->execute();
    }

    /**
     * Проверяем наступил playoff уже
     *
     * @return bool
     */
    public function isPlayoff(): bool
    {
        return in_array($this->getTournamentStatus()->getStatusId(), TournamentStatus::PLAYOFF_STATUS_ORDER);
    }

    /**
     * Находим прошедшие матчи playoff
     *
     * @param int $typeId
     * @return array|PlayoffFinishedMatch[]
     */
    public function getPrevPlayoffMatch(int $typeId)
    {
        $result = array();

        if (in_array($typeId, TeamMatch::TYPE_PLAYOFF_ORDER))
            foreach (TeamMatch::TYPE_PLAYOFF_ORDER as $order => $id) {
                if ($typeId === $id) {
                    break;
                }
                $matches                  = $this->getMatchesByType($id);
                $finishedMatches          = new PlayoffFinishedMatch();
                $finishedMatches->title   = TeamMatch::TYPE_TITLE[$id];
                $finishedMatches->matches = $matches;

                $result[] = $finishedMatches;
            }

        return array_reverse($result);
    }
}