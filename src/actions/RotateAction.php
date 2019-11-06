<?php

namespace bizley\contenttools\actions;

use Exception;
use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;

/**
 * Class RotateAction
 * @package bizley\contenttools\actions
 * @author Paweł Bizley Brzozowski
 *
 * Example action prepared for the Yii 2 ContentTools.
 *
 * This action handles rotating of the image.
 *
 * POST `direction` parameter can be:
 * - `CW` for clockwise rotation,
 * - `CCW` for counterclockwise rotation.
 *
 * Rotation is handled by the Imagine library through yii2-imagine extension.
 * JS engine can add `?_ignore=...` part to the url so it should be removed.
 * Action returns the size and URL of rotated image.
 */
class RotateAction extends Action
{
    /**
     * @var string Image upload folder. This can be Yii alias.
     * Example: /var/www/site/web/images
     * This value should be the same for all actions $uploadDir.
     * @since 1.5.0
     */
    public $uploadDir = '@webroot/content-tools-uploads';

    /**
     * @var string Web accesible path to upload folder. This can be Yii alias.
     * Example: /images
     * This value should be the same for all actions $viewPath.
     * @since 1.5.0
     */
    public $viewPath = '@web/content-tools-uploads';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!Yii::$app->request->isPost) {
            return Json::encode(['errors' => ['POST parameters are missing!']]);
        }

        try {
            $data = Yii::$app->request->post();

            if (
                empty($data['url'])
                || empty($data['direction'])
                || !in_array($data['direction'], ['CW', 'CCW'], true)
            ) {
                throw new InvalidParamException('Invalid rotate options!');
            }

            $url = trim($data['url']);
            $imageName = substr($url, strrpos($url, '/') + 1);
            if (strpos($imageName, '?_ignore=') !== false) {
                $imageName = substr($imageName, 0, strpos($imageName, '?_ignore='));
            }
            $imagePath = FileHelper::normalizePath(
                Yii::getAlias(FileHelper::normalizePath($this->uploadDir, '/'))
                . DIRECTORY_SEPARATOR
                . $imageName
            );

            $imageSizeInfo = @getimagesize($imagePath);

            if ($imageSizeInfo === false) {
                throw new InvalidParamException('Parameter "url" seems to be invalid!');
            }

            Image::getImagine()
                ->open($imagePath)
                ->copy()
                ->rotate($data['direction'] === 'CW' ? 90 : -90)
                ->save($imagePath);

            return Json::encode([
                'size' => @getimagesize($imagePath), // new size
                'url' => Yii::getAlias(FileHelper::normalizePath($this->viewPath, '/') . '/' . $imageName),
            ]);

        } catch (Exception $e) {
           return Json::encode(['errors' => [$e->getMessage()]]);
        }
    }
}
