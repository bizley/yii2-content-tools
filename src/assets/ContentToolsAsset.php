<?php

namespace bizley\contenttools\assets;

use yii\web\AssetBundle;

/**
 * Class ContentToolsAsset
 * @package bizley\contenttools\assets
 * @author Paweł Bizley Brzozowski
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
    public $js = ['content-tools.min.js'];
}
