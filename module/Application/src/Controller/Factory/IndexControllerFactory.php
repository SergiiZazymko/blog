<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 14:14
 */

namespace Application\Controller\Factory;

use Application\Controller\IndexController;
use Application\Service\PostManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class IndexControllerFactory
 * @package Application\Controller\Factory
 */
class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return IndexController|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new IndexController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(PostManager::class)
        );
    }
}
