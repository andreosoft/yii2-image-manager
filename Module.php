<?php

namespace andreosoft\imagemanager;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'andreosoft\imagemanager';

    public function init()
    {
        parent::init();

        $this->registerTranslations();
    }
    
    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['imagemanager/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@vendor/yii2-image-manager/messages',
            'forceTranslation' => true,
            'fileMap' => [
                'imagemanager/main' => 'main.php',
            ],
        ];
    }    
}
