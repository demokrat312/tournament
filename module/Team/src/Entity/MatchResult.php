<?php

namespace Team\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchResult
 *
 * @ORM\Table(name="match_result", indexes={@ORM\Index(name="match_result_match_id_fk", columns={"match_id"}), @ORM\Index(name="match_result_team_id_fk", columns={"team_win_id"})})
 * @ORM\Entity
 */
class MatchResult
{
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
     * @ORM\Column(name="result", type="string", length=10, precision=0, scale=0, nullable=true, unique=false)
     */
    private $result;

    /**
     * @var TeamMatch
     *
     * @ORM\ManyToOne(targetEntity="Team\Entity\TeamMatch")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="match_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $match;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team\Entity\Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="team_win_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $teamWin;


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
     * Set result.
     *
     * @param string|null $result
     *
     * @return MatchResult
     */
    public function setResult($result = null)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result.
     *
     * @return string|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set match.
     *
     * @param TeamMatch|null $match
     *
     * @return MatchResult
     */
    public function setMatch(TeamMatch $match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match.
     *
     * @return TeamMatch|null
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set teamWin.
     *
     * @param Team|null $teamWin
     *
     * @return MatchResult
     */
    public function setTeamWin(Team $teamWin = null)
    {
        $this->teamWin = $teamWin;

        return $this;
    }

    /**
     * Get teamWin.
     *
     * @return Team|null
     */
    public function getTeamWin()
    {
        return $this->teamWin;
    }
}
