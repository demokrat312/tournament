<?php
/**
 * Created by PhpStorm.
 * User: ogismatulin@srs.lan
 * Date: 08.12.18
 * Time: 11:54
 */

namespace Team\Classes;


/**
 * Хранить все прошедшие матчи (TeamMatch)
 *
 * Class PlayoffFinishedMatch
 * @package Team\Classes
 */
class PlayoffFinishedMatch
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var \Team\Entity\TeamMatch[]
     */
    public $matches;
}