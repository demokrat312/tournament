<?php

namespace Team\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamMatch
 *
 * @ORM\Table(name="team_match", indexes={@ORM\Index(name="match_team_id_fk_2", columns={"team2"}), @ORM\Index(name="match_team_split_id_fk", columns={"group_id"}), @ORM\Index(name="match_team_id_fk", columns={"team1"})})
 * @ORM\Entity(repositoryClass="\Team\Entity\Repository\TeamMatchRepository")
 */
class TeamMatch
{

    const TYPE_QUALIFYING = 1; // Отборочные
    const TYPE_PLAYOFF_4  = 2; // финал 4 матча
    const TYPE_PLAYOFF_2  = 3; // финал 2 матча
    const TYPE_PLAYOFF_1  = 4; // финал 1 матч

    const TYPE_ORDER = [
        0 => self::TYPE_QUALIFYING,
        1 => self::TYPE_PLAYOFF_4,
        2 => self::TYPE_PLAYOFF_2,
        3 => self::TYPE_PLAYOFF_1,
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
     * @var string|null
     *
     * @ORM\Column(name="group_name", type="string", length=10, nullable=true)
     */
    private $groupName;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team1", referencedColumnName="id", nullable=true)
     * })
     */
    private $team1;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team2", referencedColumnName="id", nullable=true)
     * })
     */
    private $team2;

    /**
     * @ORM\OneToOne(targetEntity="\Team\Entity\MatchResult", mappedBy="match", cascade={"persist", "remove"})
     */
    private $result;

    /**
     * @var int|null
     *
     * @ORM\Column(name="type_id", type="integer", nullable=true)
     */
    private $typeId;


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
     * Set groupName.
     *
     * @param string|null $groupName
     *
     * @return TeamMatch
     */
    public function setGroupName($groupName = null)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName.
     *
     * @return string|null
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set team1.
     *
     * @param Team|null $team1
     *
     * @return TeamMatch
     */
    public function setTeam1(Team $team1 = null)
    {
        $this->team1 = $team1;

        return $this;
    }

    /**
     * Get team1.
     *
     * @return Team|null
     */
    public function getTeam1()
    {
        return $this->team1;
    }

    /**
     * Set team2.
     *
     * @param Team|null $team2
     *
     * @return TeamMatch
     */
    public function setTeam2(Team $team2 = null)
    {
        $this->team2 = $team2;

        return $this;
    }

    /**
     * Get team2.
     *
     * @return Team|null
     */
    public function getTeam2()
    {
        return $this->team2;
    }

    /**
     * Set result.
     *
     * @param MatchResult|null $result
     *
     * @return TeamMatch
     */
    public function setResult(MatchResult $result = null)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result.
     *
     * @return MatchResult|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set typeId.
     *
     * @param int|null $typeId
     *
     * @return TeamMatch
     */
    public function setTypeId($typeId = null)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId.
     *
     * @return int|null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }
}
