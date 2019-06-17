<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class CommentForm
 * @package Application\Form
 */
class CommentForm extends Form
{
    /**
     * CommentForm constructor.
     */
    public function __construct()
    {
        // Определяем имя формы
        parent::__construct('comment-form');

        // Задаем POST-метод для этой формы
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
    }

    // Этот метод добавляет элементы к форме (поля ввода и кнопку отправки формы).
    protected function addElements()
    {
        // Добавляем поле "author"
        $this->add([
            'type'  => 'text',
            'name' => 'author',
            'attributes' => [
                'id' => 'author'
            ],
            'options' => [
                'label' => 'Author',
            ],
        ]);

        // Добавляем поле "comment"
        $this->add([
            'type'  => 'textarea',
            'name' => 'comment',
            'attributes' => [
                'id' => 'comment'
            ],
            'options' => [
                'label' => 'Comment',
            ],
        ]);

        // Добавляем кнопку отправки формы
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Save',
                'id' => 'submitbutton',
            ],
        ]);
    }

    // Этот метод создает фильтр входных данных (используется для фильтрации/валидации).
    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'     => 'author',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 128
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'comment',
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
    }
}
