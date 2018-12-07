<?php
/**
 * Created by PhpStorm.
 * User: ogismatulin@srs.lan
 * Date: 07.12.18
 * Time: 9:44
 */

namespace Team\Factory\Service;


use Interop\Container\ContainerInterface;
use Team\Service\TeamManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class TeamManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     * @return TeamManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new TeamManager($em);
    }
}