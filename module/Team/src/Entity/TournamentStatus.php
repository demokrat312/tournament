<?php

namespace Team\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TournamentStatus
 *
 * @ORM\Table(name="tournament_status")
 * @ORM\Entity
 */
class TournamentStatus
{
    const STATUS_CREATE_TEAM         = 1; // Создание команды
    const STATUS_SPLIT_TEAM          = 2; // Распределение по группам и матчам
    const STATUS_SPLIT_MATCH         = 3; // Жеребьевка
    const STATUS_QUALIFYING          = 4; // Отборочные
    const STATUS_SPLIT_MATCH_PLAYOFF = 5; // Жеребьевка для playoff
    const STATUS_PLAYOFF_4           = 6; // Финальные матчи. 1/4
    const STATUS_PLAYOFF_2           = 7; // Финальные матчи. 1/2 полуфина
    const STATUS_PLAYOFF_1           = 8; // Финальные матчи. финал

    CONST PLAYOFF_STATUS_ORDER = [
        0 => self::STATUS_SPLIT_MATCH_PLAYOFF,
        1 => self::STATUS_PLAYOFF_4,
        2 => self::STATUS_PLAYOFF_2,
        3 => self::STATUS_PLAYOFF_1,
    ];
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="status_id", type="smallint", precision=0, scale=0, nullable=false, unique=false)
     */
    private $statusId;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set statusId.
     *
     * @param int $statusId
     *
     * @return TournamentStatus
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;

        return $this;
    }

    /**
     * Get statusId.
     *
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }
}
