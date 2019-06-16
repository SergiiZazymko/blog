<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 17:30
 */

namespace Application\Controller\Factory;

use Application\Controller\PostController;
use Application\Form\PostForm;
use Application\Service\PostManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PostControllerFactory
 * @package Application\Controller\Factory
 */
class PostControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PostController(
            $container->get('FormElementManager')->get(PostForm::class),
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(PostManager::class)
        );
    }
}
