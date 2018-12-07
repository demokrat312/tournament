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
    public function findByGroup(string $groupName)
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->join('t.group', 'g')
            ->where($qb->expr()->eq('g.groupName', ':groupName'))
            ->setParameter('groupName', $groupName);

        return $qb->getQuery()->getResult();
    }

    public function getCount()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
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
            $teams[] = $result->getTeamWin();
        }


        return $teams;
    }

    public function getLastPlayOffTypeId() {
        $qb = $this->getEntityManager()->getRepository(TeamMatch::class)->createQueryBuilder('mat');

        $qb
            ->select('max(mat.typeId)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}