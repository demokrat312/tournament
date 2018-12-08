<?php

namespace Team\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamSplit
 *
 * @ORM\Table(name="team_split", uniqueConstraints={@ORM\UniqueConstraint(name="team_split_team_id_uindex", columns={"team_id"})})
 * @ORM\Entity
 */
class TeamSplit
{
    const GROUP1 = 'A';
    const GROUP2 = 'B';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $groupName;

    /**
     * @var \Team\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Team\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $team;


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
     * @param string $groupName
     *
     * @return TeamSplit
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;

        return $this;
    }

    /**
     * Get groupName.
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set team.
     *
     * @param \Team\Entity\Team|null $team
     *
     * @return TeamSplit
     */
    public function setTeam(\Team\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team.
     *
     * @return \Team\Entity\Team|null
     */
    public function getTeam()
    {
        return $this->team;
    }
}
