<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 16:11
 */

namespace Application\Form;

use Application\Entity\Post;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\Filter\StripTags;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;

/**
 * Class PostForm
 * @package Application\Form
 */
class PostForm extends Form
{
    /**
     * @inheritDoc
     */
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('post-form');

        $this->setAttribute('method', 'post');
    }

    /**
     *
     */
    public function init()
    {
        $this->addElements()
            ->addInputFilter();
    }

    /**
     * @return $this
     */
    protected function addElements()
    {
        $this->add([
            'type' => 'text',
            'name' => 'title',
            'attributes' => [
                'id' => 'title',
            ],
            'options' => [
                'label' => 'Title',
            ],
        ]);

        $this->add([
            'type' => 'textarea',
            'name' => 'content',
            'attributes' => [
                'id' => 'content',
            ],
            'options' => [
                'label' => 'Content',
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'tags',
            'attributes' => [
                'id' => 'tags',
            ],
            'options' => [
                'label' => 'Tags',
            ],
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'status',
            'attributes' => [
                'id' => 'status',
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    Post::STATUS_PUBLISHED => 'Published',
                    Post::STATUS_DRAFT => 'Draft',
                ],
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submi',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Create',
            ],
        ]);

        return $this;
    }

    /**
     *
     */
    private function addInputFilter()
    {
        /** @var InputFilter $inputFilter */
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'     => 'title',
            'required' => true,
            'filters'  => [
                ['name' => StringTrim::class],
                ['name' => StripTags::class],
                ['name' => StripNewlines::class],
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 1024
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'content',
            'required' => true,
            'filters'  => [
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 4096
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'tags',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 1024
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'status',
            'required' => true,
            'validators' => [
                [
                    'name' => 'InArray',
                    'options'=> [
                        'haystack' => [Post::STATUS_PUBLISHED, Post::STATUS_DRAFT],
                    ]
                ],
            ],
        ]);
    }
}
