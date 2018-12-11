<?php

namespace bizley\contenttools\assets;

use yii\web\AssetBundle;

/**
 * @author Paweł Bizley Brzozowski
 * @version 1.1.0
 * @license Apache 2.0
 * https://github.com/bizley/yii2-content-tools
 * http://www.yiiframework.com/extension/yii2-content-tools
 * 
 * ContentTools has been created by Anthony Blackshaw
 * http://getcontenttools.com/
 * https://github.com/GetmeUK/ContentTools
 * 
 * The ContentTools assets.
 */
class ContentToolsAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bower/contenttools/build';
    
    /**
     * {@inheritdoc}
     */
    public $css = ['content-tools.min.css'];
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->js[] = 'content-tools' . (YII_DEBUG ? '' : '.min') . '.js';
        parent::init();
    }
}
