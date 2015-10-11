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

namespace bizley\contenttools\assets;

use yii\web\AssetBundle;

/**
 * The ContentTools files.
 * As soon as the author of ContentTools adds it to Bower or NPM packages list 
 * I will remove the ContentTools folder so it will be downloaded using 
 * composer asset plugin.
 */
class ContentToolsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/bizley/content-tools/ContentTools/build';
    
    /**
     * @inheritdoc
     */
    public $css = ['content-tools.min.css'];
    
    /**
     * @inheritdoc
     */
    public $js = ['content-tools.min.js'];
}