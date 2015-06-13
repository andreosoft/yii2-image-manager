<?php

namespace andreosoft\imagemanager;

use yii\widgets\InputWidget;
use yii\helpers\Html;


class Image extends InputWidget
{
    public function init()
    {
        parent::init();
        $view = $this->getView();
        $view->registerJs($this->render('js.js'));
    }

    public function run()
    {
        echo Html::a(Html::img(\andreosoft\image\Image::thumb($this->model[$this->attribute], 100, 100),  ['data-placeholder' => \andreosoft\image\Image::thumb('', 100, 100)]), "", ['id' => 'thumb-image', 'data-toggle' => 'image', 'class' => 'img-thumbnail']);
        echo Html::hiddenInput(\yii\helpers\BaseHtml::getInputName($this->model, $this->attribute), $this->model[$this->attribute], ['id' => 'input-image']);

    }
}
