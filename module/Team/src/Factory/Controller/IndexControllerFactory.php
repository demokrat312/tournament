<?php
/**
 * Created by PhpStorm.
 * User: ogismatulin@srs.lan
 * Date: 06.12.18
 * Time: 14:52
 */

namespace Team\Factory\Controller;

use Interop\Container\ContainerInterface;
use Team\Service\TeamManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Team\Controller\IndexController;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $teamManager = $container->get(TeamManager::class);

        return new IndexController($teamManager);
    }
}