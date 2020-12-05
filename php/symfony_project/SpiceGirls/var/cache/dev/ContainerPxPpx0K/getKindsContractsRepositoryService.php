<?php

namespace ContainerPxPpx0K;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getKindsContractsRepositoryService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Repository\KindsContractsRepository' shared autowired service.
     *
     * @return \App\Repository\KindsContractsRepository
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/persistence/lib/Doctrine/Persistence/ObjectRepository.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/collections/lib/Doctrine/Common/Collections/Selectable.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/lib/Doctrine/ORM/EntityRepository.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/Repository/ServiceEntityRepositoryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/Repository/ServiceEntityRepository.php';
        include_once \dirname(__DIR__, 4).'/src/Repository/KindsContractsRepository.php';

        return $container->privates['App\\Repository\\KindsContractsRepository'] = new \App\Repository\KindsContractsRepository(($container->services['doctrine'] ?? $container->getDoctrineService()));
    }
}
