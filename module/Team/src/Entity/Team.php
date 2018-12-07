<?php

namespace Team\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="\Team\Entity\Repository\TeamRepository")
 */
class Team
{
    const TEAM_AMOUNT = 16;

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
     * @ORM\Column(name="title", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $title;

    /**
     * @ORM\OneToOne(targetEntity="\Team\Entity\TeamSplit", mappedBy="team", cascade={"persist", "remove"})
     */
    private $group;

    /**
     * @var MatchResult
     */
    public $result;


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
     * Set title.
     *
     * @param string|null $title
     *
     * @return Team
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set group.
     *
     * @param TeamSplit|null $group
     *
     * @return Team
     */
    public function setGroup(TeamSplit $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group.
     *
     * @return TeamSplit|null
     */
    public function getGroup()
    {
        return $this->group;
    }
}
