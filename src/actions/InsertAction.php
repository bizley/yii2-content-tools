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
 * Class InsertAction
 * @package bizley\contenttools\actions
 * @author Paweł Bizley Brzozowski
 *
 * Example action prepared for the Yii 2 ContentTools.
 *
 * This action handles the cropping of image.
 *
 * POST `crop` parameter is the string with comma separated list of cropping marks positions in order:
 * top, left, bottom, right.
 *
 * Position value can by anything between 0 and 1 so:
 * - `0,0,1,1` means full image with no cropping and
 * - `0.25,0.25,0.75,0.75` means that box half the size of the full image in the center needs to be cropped from it.
 *
 *   0        1
 * 0 +--------+
 *   |        |
 *   |        |
 *   |        |
 * 1 +--------+
 *
 * Cropping is handled by the Imagine library through yii2-imagine extension.
 * JS engine can add `?_ignore=...` part to the url so it should be removed.
 * If `crop` parameter is not set image should be inserted in the content as-is.
 * Action returns the size, URL, and alternative description of image (cropped or not).
 */
class InsertAction extends Action
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

            if (empty($data['url'])) {
                throw new InvalidParamException('Parameter "url" is missing!');
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

            if (!empty($data['crop'])) {
                $crop = explode(',', $data['crop']);

                if (count($crop) !== 4) {
                    throw new InvalidParamException('Parameter "crop" is invalid!');
                }

                $positions = [];

                foreach ($crop as $position) {
                    $position = trim($position);

                    if (!is_numeric($position) || $position < 0 || $position > 1) {
                        throw new InvalidParamException('Parameter "crop" contains invalid value!');
                    }

                    $positions[] = $position;
                }

                list($width, $height) = $imageSizeInfo;

                Image::crop(
                    $imagePath,
                    floor($width * $positions[3] - $width * $positions[1]),
                    floor($height * $positions[2] - $height * $positions[0]),
                    [
                        floor($width * $positions[1]),
                        floor($height * $positions[0])
                    ]
                )->save($imagePath);
            }

            return Json::encode([
                'size' => @getimagesize($imagePath), // not using $imageSizeInfo because it's new size
                'url' => Yii::getAlias(FileHelper::normalizePath($this->viewPath, '/') . '/' . $imageName),
                'alt' => $imageName
            ]);

        } catch (Exception $e) {
            return Json::encode(['errors' => [$e->getMessage()]]);
        }
    }
}
