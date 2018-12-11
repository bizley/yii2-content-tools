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
     * {@inheritdoc}
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $model = new ImageForm();
            $model->image = UploadedFile::getInstanceByName('image');

            if ($model->validate()) {
                if ($model->upload()) {
                    $imageSizeInfo = @getimagesize($model->url);

                    if ($imageSizeInfo === false) {
                        return Json::encode(['errors' => ['Image URL seems to be invalid!']]);
                    }

                    list($width, $height) = $imageSizeInfo;

                    return Json::encode([
                        'size' => [$width, $height],
                        'url'  => $model->url
                    ]);
                }
            } else {
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

        return Json::encode(['errors' => ['POST data is missing!']]);
    }
}
