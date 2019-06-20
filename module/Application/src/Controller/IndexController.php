<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Entity\Post;
use Application\Repository\PostRepository;
use Application\Service\PostManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\View\Model\ViewModel;

/**
 * Class IndexController
 * @package Application\Controller
 */
class IndexController extends AbstractActionController
{
    /** @var EntityManager $dem */
    private $dem;

    /** @var PostManager $postManager */
    private $postManager;

    /**
     * IndexController constructor.
     * @param EntityManager $dem
     */
    public function __construct(EntityManager $dem, PostManager $postManager)
    {
        $this->dem = $dem;
        $this->postManager = $postManager;
    }

    /**
     * @return ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        /** @var string $page */
        $page = $this->params()->fromQuery('page', 1);

        /** @var PostRepository $postRepostory */
        $postRepostory =  $this->dem->getRepository(Post::class);

        if ($tag = $this->params()->fromQuery('tag')) {
            $posts = $postRepostory->findAllByTagName($tag);
        } else {
            /** @var array $posts */
            $posts = $postRepostory->findBy(
                ['status' => Post::STATUS_PUBLISHED],
                ['dateCreated' => 'DESC']
            );
        }

        /** @var Paginator $paginator */
        $paginator = $this->createPaginator($posts);
        $paginator->setCurrentPageNumber($page);

        /** @var array $tagCloud */
        $tagCloud = $this->postManager->getTagCloud();

        return new ViewModel([
            'posts' => $paginator,
            'tagCloud' => $tagCloud,
            'postManager' => $this->postManager,
            'tag' => $tag,
        ]);
    }

    /**
     * @param array $items
     * @return Paginator
     */
    private function createPaginator(array $items): Paginator
    {
        /** @var Paginator $paginator */
        $paginator = new Paginator(new ArrayAdapter($items));
        $paginator->setItemCountPerPage(PostController::ITEM_COUNT_PER_PAGE);

        return $paginator;
    }
}
