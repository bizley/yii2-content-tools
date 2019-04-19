<?php

namespace bizley\contenttools\actions;

use bizley\contenttools\models\ImageForm;
use Yii;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Class UploadAction
 * @package bizley\contenttools\actions
 * @author Paweł Bizley Brzozowski
 *
 * Example action prepared for the Yii 2 ContentTools.
 *
 * This action handles validation of the uploaded image and saving it.
 *
 * Validation is done using the `ImageForm`.
 * Action returns the size and URL of uploaded image.
 */
class UploadAction extends Action
{
    /**
     * @var string Image upload folder. This can be Yii alias.
     * Example: /var/www/site/web/images
     * @since 1.4.0
     */
    public $uploadDir = '@webroot/content-tools-uploads';

    /**
     * @var string Web accesible path to upload folder. This can be Yii alias.
     * Example: /images
     * @since 1.4.0
     */
    public $viewPath = '@web/content-tools-uploads';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!Yii::$app->request->isPost) {
            return Json::encode(['errors' => ['POST data is missing!']]);
        }

        $model = new ImageForm([
            'uploadDir' => $this->uploadDir,
            'viewPath' => $this->viewPath,
            'image' => UploadedFile::getInstanceByName('image'),
        ]);

        if ($model->validate()) {
            if ($model->upload()) {
                $imageSizeInfo = @getimagesize($model->path);

                if ($imageSizeInfo === false) {
                    return Json::encode(['errors' => ['Image upload path seems to be invalid!']]);
                }

                return Json::encode([
                    'size' => $imageSizeInfo,
                    'url'  => $model->url
                ]);
            }

            return Json::encode(['errors' => ['Image upload error!']]);
        }

        $errors = [];
        $modelErrors = $model->getErrors();

        foreach ($modelErrors as $field => $fieldErrors) {
            foreach ($fieldErrors as $fieldError) {
                $errors[] = $fieldError;
            }
        }

        if (empty($errors)) {
            $errors = ['Unknown file upload validation error!'];
        }

        return Json::encode(['errors' => $errors]);
    }
}
