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
    /**
     * @deprecated since 1.4.0 - use setUploadDir() and setViewPath() instead
     */
    const UPLOAD_DIR = 'content-tools-uploads';

    /**
     * @var UploadedFile Uploaded image
     */
    public $image;

    /**
     * @var string Web accessible path to the uploaded image. This property is automatically filled after successful
     * upload based on the $viewPath property.
     */
    public $url;

    /**
     * @var string Path of the uploaded image. This property is automatically filled after successful upload based on
     * the $uploadDir property.
     * @since 1.4.0
     */
    public $path;

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

    private $_uploadDir;

    /**
     * Sets image upload folder. This can be Yii alias.
     * @param string $path
     * @since 1.4.0
     */
    public function setUploadDir($path)
    {
        $this->_uploadDir = $path;
    }

    private $_viewPath;

    /**
     * Sets web accesible path to upload folder. This can be Yii alias.
     * @param string $path
     * @since 1.4.0
     */
    public function setViewPath($path)
    {
        $this->_viewPath = $path;
    }

    /**
     * Validates and saves the image.
     * Creates the folder to store images if necessary.
     * @return bool
     */
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }

        try {
            // first normalize for alias translating then for OS
            $save_path = FileHelper::normalizePath(Yii::getAlias(FileHelper::normalizePath($this->_uploadDir, '/')));
            FileHelper::createDirectory($save_path);

            $image = $this->image->baseName . '.' . $this->image->extension;
            $this->path = $save_path . DIRECTORY_SEPARATOR . $image;
            $this->url = Yii::getAlias(FileHelper::normalizePath($this->_viewPath, '/') . '/' . $image);

            return $this->image->saveAs($this->path);

        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }

        return false;
    }
}
