<?php

namespace andreosoft\imagemanager;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\data\Pagination;
use andreosoft\image\Image;

class ImageController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $this->redirect(['/users/admin/login']);
                },
                        'rules' => [
                            [
                                'actions' => ['index', 'upload', 'folder', 'delete'],
                                'allow' => true,
                                'roles' => ['manager', 'admin'],
                            ],                            
                        ],
                    ],
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['post'],
                        ],
                    ],
                ];
            }

            public function actionIndex($filter_name = '', $directory = '/images', $page = 1, $target = '', $thumb = '') {
                $filter_name = rtrim(str_replace(array('../', '..\\', '..', '*'), '', $filter_name), '/');
                $uploads = \Yii::getAlias('@uploads');
                $directory = rtrim(str_replace(array('../', '..\\', '..'), '', $directory), '/');

                $directories = glob($uploads . $directory . '/' . $filter_name . '*', GLOB_ONLYDIR);
                if (!$directories) {
                    $directories = array();
                }
                $files = glob($uploads . $directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

                if (!$files) {
                    $files = array();
                }

                $images = array_merge($directories, $files);
                $image_total = count($images);

                $images = array_splice($images, ($page - 1) * 16, 16);

                foreach ($images as $image) {
                    $name = str_split(basename($image), 14);

                    if (is_dir($image)) {
                        $data['images'][] = array(
                            'thumb' => '',
                            'name' => implode(' ', $name),
                            'type' => 'directory',
                            'path' => substr($image, strlen($uploads)),
                            'href' => '',
                        );
                    } elseif (is_file($image)) {
                        $data['images'][] = array(
                            'thumb' => Image::thumb(substr($image, strlen($uploads)), 100, 100),
                            'name' => implode(' ', $name),
                            'type' => 'image',
                            'path' => substr($image, strlen($uploads)),
                            'href' => '@webuploads' . substr($image, strlen($uploads)),
                        );
                    }
                }

                $pos = strrpos($directory, '/');
                $parent = '';
                if ($pos) {
                    $parent = (substr($directory, 0, $pos));
                }
                $pagination = new Pagination([
                    'defaultPageSize' => 16,
                    'totalCount' => $image_total,
                ]);

                return $this->renderAjax('index', [
                            'images' => $data['images'],
                            'parent' => $parent,
                            'current' => $directory,
                            'target' => $target,
                            'thumb' => $thumb,
                            'pagination' => $pagination,
                ]);
            }

            public function actionFolder($directory) {
                $json = array();
                $folder = Yii::$app->request->post()['folder'];
                $uploads = \Yii::getAlias('@uploads');
                if (!is_dir($uploads . $directory)) {
                    $json['error'] = \Yii::t('filemanager/main', 'Warning: Directory does not exist!');
                } else {
                    $folder = str_replace(array('../', '..\\', '..'), '', basename(html_entity_decode($folder, ENT_QUOTES, 'UTF-8')));
                    if ((strlen($folder) < 1) || (strlen($folder) > 128)) {
                        $json['error'] = \Yii::t('filemanager/main', 'Warning: Folder name must be a between 1 and 255!');
                    }
                    if (is_dir($uploads . $directory . '/' . $folder)) {
                        $json['error'] = \Yii::t('filemanager/main', 'Warning: A file or directory with the same name already exists!');
                    }
                }
                if (!$json) {
                    mkdir($uploads . $directory . '/' . $folder, 0777);
                    $json['success'] = \Yii::t('filemanager/main', 'Success: Directory created!');
                }

                return json_encode($json);
            }

            public function actionDelete() {
                $uploads = \Yii::getAlias('@uploads');
                $paths = Yii::$app->request->post('path');

                foreach ($paths as $path) {
                    $path = rtrim($uploads . str_replace(array('../', '..\\', '..'), '', $path), '/');

                    // If path is just a file delete it
                    if (is_file($path)) {
                        unlink($path);

                        // If path is a directory beging deleting each file and sub folder
                    } elseif (is_dir($path)) {
                        $files = array();

                        // Make path into an array
                        $path = array($path . '*');

                        // While the path array is still populated keep looping through
                        while (count($path) != 0) {
                            $next = array_shift($path);

                            foreach (glob($next) as $file) {
                                // If directory add to path array
                                if (is_dir($file)) {
                                    $path[] = $file . '/*';
                                }

                                // Add the file to the files to be deleted array
                                $files[] = $file;
                            }
                        }

                        // Reverse sort the file array
                        rsort($files);

                        foreach ($files as $file) {
                            // If file just delete
                            if (is_file($file)) {
                                unlink($file);

                                // If directory use the remove directory function
                            } elseif (is_dir($file)) {
                                rmdir($file);
                            }
                        }
                    }
                }

                $json['success'] = \Yii::t('filemanager/main', 'Success: Your file or directory has been deleted!');
                return json_encode($json);
            }

            public function actionUpload($directory) {
                $json = array();
                $model = new FileImage();
                $model->file = UploadedFile::getInstances($model, 'file');
                    if ($model->file && $model->validate()) {
                        $uploads = \Yii::getAlias('@uploads');
                        foreach ($model->file as $file) {
                            $fileName = $uploads . $directory . '/' . $file;
                            if ($file->saveAs($fileName)) {
                                $json['success'] = \Yii::t('filemanager/main', 'Success: Your file has been uploaded!');
                            }
                        }
                    }
                    if (!json) {
                        $json['error'] = \Yii::t('filemanager/main', 'Warning: File could not be uploaded for an unknown reason!');
                    }

                return json_encode($json);
            }

            
            protected function registerClientScript() {
                $view = $this->getView();
                Asset::register($view);
            }

        }
        