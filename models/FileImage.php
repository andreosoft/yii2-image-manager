<?php
namespace andreosoft\imagemanager;

use yii\helpers\Url;

class FileImage extends \yii\base\Model {

    public $file;



    public function rules() {
        return [
            [['file'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png', 'maxFiles' => 50],
            [['file'], 'file', 'maxFiles' => 50],
        ];
    }
    
    public function attributeHints() {
        return [];
    }

}
