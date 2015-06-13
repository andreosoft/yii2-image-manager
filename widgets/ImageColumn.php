<?php

namespace andreosoft\imagemanager;

use yii\helpers\Html;

class ImageColumn extends \yii\grid\DataColumn {

    public function getDataCellValue($model, $key, $index) {

        $html = Html::a(Html::img(\andreosoft\image\Image::thumb($model[$this->attribute], 100, 100), ['data-placeholder' => \andreosoft\image\Image::thumb('', 100, 100)]), "", ['id' => "thumb-image-{$key}", 'data-toggle' => 'image', 'class' => 'img-thumbnail']);
        $html .= Html::hiddenInput(\yii\helpers\BaseHtml::getInputName($model, $this->attribute), $model[$this->attribute], ['id' => "input-image-{$key}"]);

        return $html;
    }

    protected function renderDataCellContent($model, $key, $index) {
        if ($this->content === null) {
            return $this->getDataCellValue($model, $key, $index);
        } else {
            return parent::renderDataCellContent($model, $key, $index);
        }
    }

    public function renderFooterCellJS($model) {
        return Html::tag('td', $this->renderFooterCellContentJS($model), $this->footerOptions);
    }

    protected function renderFooterCellContentJS($model) {
        $key = '';
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            } else {
                return call_user_func($this->value, $model, '', '', $this);
            }
        } elseif ($this->attribute !== null) {

//            $html = '<a id="thumb-image-\'+data.id+\'" class="img-thumbnail" href="/index.php?r=catalog%2Fadmin%2Fupdate&amp;id=17" data-toggle="image">';
//            $html .= Html::img(\andreosoft\image\Image::thumb($model[$this->attribute], 100, 100), ['data-placeholder' => \andreosoft\image\Image::thumb('', 100, 100)]);
//            $html .= '</a>';
//            $html .= '<input type="hidden" id="input-image-\'+data.id+\'" name="' . \yii\helpers\BaseHtml::getInputName($model, $this->attribute) . '">';

            return $html;
        }
        return null;
    }

}
