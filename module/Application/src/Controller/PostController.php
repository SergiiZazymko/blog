<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 17:20
 */

namespace Application\Controller;

use Application\Entity\Post;
use Application\Form\PostForm;
use Application\Service\PostManager;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Class PostController
 * @package Application\Controller
 */
class PostController extends AbstractActionController
{
    /** @var PostForm $form */
    private $form;

    /** @var EntityManager $dem */
    private $dem;

    /** @var PostManager $postManager */
    private $postManager;

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

    /**
     * PostController constructor.
     * @param PostForm $form
     * @param EntityManager $dem
     * @param PostManager $posManager
     */
    public function __construct(PostForm $form, EntityManager $dem, PostManager $postManager)
    {
        $this->form = $form;
        $this->dem = $dem;
        $this->postManager = $postManager;
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function addAction()
    {
        if ($this->getRequest()->isPost() && $this->form->setData($this->params()->fromPost())->isValid()) {
            $this->postManager->addNewPost($this->form->getData());

            return $this->redirect()->toRoute('application');
        }

        return new ViewModel([
            'form' => $this->form,
        ]);
    }

    /**
     *
     */
    public function editAction()
    {
        /** @var string $id */
        $id = $this->params()->fromRoute('id');

        /** @var Post $post */
        $post = $this->dem->getRepository(Post::class)->find($id);

        if (!$post) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->isPost()) {

        } else {
            /** @var array $data */
            $data = [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'status' => $post->getStatus(),
                'tags' => $this->postManager->convertTagsToString($post),
            ];

            $this->form->setData($data);
        }

        return new ViewModel([
            'form' => $this->form,
        ]);
    }
}
