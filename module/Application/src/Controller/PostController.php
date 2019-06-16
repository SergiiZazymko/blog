<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 17:20
 */

namespace Application\Controller;

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
}
