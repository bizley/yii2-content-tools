<?php

/**
 * @author Paweł Bizley Brzozowski
 * @version 1.0
 * @license Apache 2.0
 * https://github.com/bizley-code/yii2-content-tools
 * http://www.yiiframework.com/extension/yii2-content-tools
 * 
 * ContentTools was created by Anthony Blackshaw
 * http://getcontenttools.com/
 * https://github.com/GetmeUK/ContentTools
 */

namespace bizley\contenttools\models;

use yii\base\Model;
use yii\helpers\FileHelper;

/**
 * This model is used by UploadAction to validate and save the image uploaded 
 * through ContentTools editor.
 * Images are stored in the 'content-tools-uploads' folder.
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['image', 'image', 'extensions' => ['png', 'jpg', 'gif'], 'maxWidth' => 1000, 'maxHeight' => 1000, 'maxSize' => 2 * 1024 * 1024]
        ];
    }
    
    /**
     * Validates and saves the image.
     * Creates the folder to store images if necessary.
     * @return boolean
     */
    public function upload()
    {
        if ($this->validate()) {
            FileHelper::createDirectory(self::UPLOAD_DIR);
            $this->url = self::UPLOAD_DIR . '/' . $this->image->baseName . '.' . $this->image->extension;
            return $this->image->saveAs($this->url);
        }
        return false;
    }
}
