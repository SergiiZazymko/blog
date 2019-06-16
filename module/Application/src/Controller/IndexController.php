<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Entity\Post;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /** @var EntityManager $dem */
    private $dem;

    /**
     * IndexController constructor.
     * @param EntityManager $dem
     */
    public function __construct(EntityManager $dem)
    {
        $this->dem = $dem;
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        /** @var array $posts */
        $posts = $this->dem->getRepository(Post::class)
            ->findBy(
                ['status' => Post::STATUS_PUBLISHED],
                ['dateCreated' => 'DESC']
            );

        return new ViewModel([
            'posts' => $posts,
        ]);
    }
}
