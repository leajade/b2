<?php

namespace ContainerPxPpx0K;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getOfferFormTypeService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Form\OfferFormType' shared autowired service.
     *
     * @return \App\Form\OfferFormType
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/FormTypeInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/form/AbstractType.php';
        include_once \dirname(__DIR__, 4).'/src/Form/OfferFormType.php';

        return $container->privates['App\\Form\\OfferFormType'] = new \App\Form\OfferFormType();
    }
}
