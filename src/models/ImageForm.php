<?php

namespace bizley\contenttools\models;

use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class ImageForm
 * @package bizley\contenttools\models
 * @author Paweł Bizley Brzozowski
 *
 * This model is used by `bizley\contenttools\actions\UploadAction` to validate and save the image uploaded
 * through Yii 2 ContentTools editor.
 *
 * Images are stored in the `content-tools-uploads` web accessible folder.
 */
class ImageForm extends Model
{
    const UPLOAD_DIR = 'content-tools-uploads';

    /**
     * @var UploadedFile Uploaded image
     */
    public $image;
    
    /**
     * @var string Web accessible path to the uploaded image
     */
    public $url;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                'image',
                'image',
                'extensions' => [
                    'png',
                    'jpg',
                    'jpeg',
                    'gif'
                ],
                'maxWidth' => 1000,
                'maxHeight' => 1000,
                'maxSize' => 2 * 1024 * 1024
            ]
        ];
    }
    
    /**
     * Validates and saves the image.
     * Creates the folder to store images if necessary.
     * @return bool
     */
    public function upload()
    {
        try {
            if ($this->validate()) {
                $save_path = FileHelper::normalizePath(Yii::getAlias('@app/web/' . self::UPLOAD_DIR));

                FileHelper::createDirectory($save_path);

                $this->url = Yii::getAlias('@web/' . self::UPLOAD_DIR . '/' . $this->image->baseName . '.' . $this->image->extension);

                return $this->image->saveAs(FileHelper::normalizePath($save_path . '/' . $this->image->baseName . '.' . $this->image->extension));
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }

        return false;
    }
}
