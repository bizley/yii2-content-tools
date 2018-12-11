<?php

namespace bizley\contenttools\assets;

use yii\web\AssetBundle;

/**
 * Class ContentToolsImagesAsset
 * @package bizley\contenttools\assets
 * @author Paweł Bizley Brzozowski
 *
 * Default JS for the images engine.
 */
class ContentToolsImagesAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@bizley/contenttools/js';
    
    /**
     * {@inheritdoc}
     */
    public $js = ['content-tools-images.js'];
    
    /**
     * {@inheritdoc}
     */
    public $depends = ['bizley\contenttools\assets\ContentToolsAsset'];
}
