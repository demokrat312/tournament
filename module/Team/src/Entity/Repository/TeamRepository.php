<?php
/**
 * Created by PhpStorm.
 * User: ogismatulin@srs.lan
 * Date: 07.12.18
 * Time: 13:34
 */

namespace Team\Entity\Repository;


use Doctrine\ORM\EntityRepository;
use Team\Entity\MatchResult;
use Team\Entity\Team;
use Team\Entity\TeamMatch;

class TeamRepository extends EntityRepository
{
    /**
     * Находим команды из определенной группы A или B
     *
     * @param string $groupName
     * @return mixed
     */
    public function findByGroup(string $groupName)
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->join('t.group', 'g')
            ->where($qb->expr()->eq('g.groupName', ':groupName'))
            ->setParameter('groupName', $groupName);

        return $qb->getQuery()->getResult();
    }

    /**
     * Сколько созданно команд
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Находим команды которые победили на определенном этапе
     *
     * @param int $typeId
     * @return Team[]
     */
    public function findWin(int $typeId)
    {
        $qb = $this->getEntityManager()->getRepository(MatchResult::class)->createQueryBuilder('r');

        $qb
            ->join('r.match', 'match')
            ->where($qb->expr()->eq('match.typeId', ':typeId'))
            ->setParameter('typeId', $typeId);

        /** @var MatchResult[] $results */
        $results = $qb->getQuery()->getResult();
        $teams   = array();
        foreach ($results as $result) {
            $team = $result->getTeamWin();
            $team->result = $result;
            $teams[] = $team;
        }


        return $teams;
    }

    /**
     * Получаем последний тип матча
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastMatchTypeId() {
        $qb = $this->getEntityManager()->getRepository(TeamMatch::class)->createQueryBuilder('mat');

        $qb
            ->select('max(mat.typeId)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}