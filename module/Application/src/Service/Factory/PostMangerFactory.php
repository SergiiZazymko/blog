<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 17:13
 */

namespace Application\Service\Factory;

use Application\Service\PostManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PostMangerFactory
 * @package Application\Service\Factory
 */
class PostMangerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PostManager(
            $container->get('doctrine.entitymanager.orm_default')
        );
    }
}
