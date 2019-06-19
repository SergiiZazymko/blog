<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 17:20
 */

namespace Application\Controller;

use Application\Entity\Post;
use Application\Form\CommentForm;
use Application\Form\PostForm;
use Application\Service\PostManager;
use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
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
            'postManager' => $this->postManager,
        ]);
    }

    /**
     * @return void|\Zend\Http\Response|ViewModel
     */
    public function viewAction()
    {
        /** @var Form $form */
        $form = new CommentForm;

        /** @var int $id */
        $id = $this->params()->fromRoute('id', -1);

        /** @var Post $post */
        $post = $this->dem->getRepository(Post::class)->find($id);

        if (!$post) {
            $this->getRequest()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->isPost() && $form->setData($this->params()->fromPost())->isValid()) {
            $this->postManager->addCommentToPost($post, $form->getData());

            return $this->redirect()->toRoute('posts', ['action' => 'view', 'id' => $post->getId()]);
        }

        return new ViewModel([
            'post' => $post,
            'commentCount' => $this->postManager->getCommentCountStr($post),
            'form' => $form,
        ]);

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
     * @return void|\Zend\Http\Response|ViewModel
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
            /** @var array $data */
            $data = $this->params()->fromPost();

            $this->postManager->editPost($post, $data);
            return $this->redirect()->toRoute('posts');
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

    /**
     * @return void|\Zend\Http\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function deleteAction()
    {
        /** @var string $id */
        $id = $this->params()->fromRoute('id', -1);

        /** @var Post $post */
        $post = $this->dem->getRepository(Post::class)->find($id);

        if (!$post) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->postManager->removePost($post);

        return $this->redirect()->toRoute('posts');
    }
}
