<?php
/**
 * Created by PhpStorm.
 * User: ogismatulin@srs.lan
 * Date: 07.12.18
 * Time: 13:34
 */

namespace Team\Entity\Repository;


use Doctrine\ORM\EntityRepository;

class TeamMatchRepository extends EntityRepository
{
    public function findAllByType(int $typeId)
    {
        return $this->findBy(['typeId' => $typeId]);
    }
}