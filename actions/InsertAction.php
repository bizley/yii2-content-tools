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

namespace bizley\contenttools\actions;

use Exception;
use Yii;
use yii\base\Action;
use yii\helpers\Json;
use yii\imagine\Image;

/**
 * Example action prepared for the yii2-content-tools.
 * This action handles the cropping of image.
 * 'crop' parameter is the list of cropping marks positions in order:
 * - top,
 * - left,
 * - bottom, 
 * - right.
 * Position value can by anything between 0 and 1 so '0,0,1,1' means full image 
 * with no cropping and '0.25,0.25,0.75,0.75' means that box half the size of the 
 * full image in the center needs to be cropped from it.
 * Cropping is handled by the Imagine library through yii2-imagine extension.
 * JS engine can add '?_ignore=...' part to the url so it should be removed.
 * If 'crop' parameter is not set image should be inserted in the content as-is.
 * Action returns the size, url and alternative description of image (cropped or not).
 */
class InsertAction extends Action
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            if (!empty($data['url'])) {
                try {
                    $crop = [];
                    if (!empty($data['crop'])) {
                        $crop = explode(',', $data['crop']);
                        if (count($crop) !== 4) {
                            return Json::encode(['errors' => ['Invalid crop options!']]);
                        }
                        foreach ($crop as $c) {
                            if (!is_numeric(trim($c)) || trim($c) < 0 || trim($c) > 1) {
                                return Json::encode(['errors' => ['Invalid crop options!']]);
                            }
                        }
                    }
                    
                    $url = $data['url'];
                    if (substr($url, 0, 1) == '/') {
                        $url = substr($url, 1);
                    }
                    if (strpos($url, '?_ignore=') !== false) {
                        $url = substr($url, 0, strpos($url, '?_ignore='));
                    }
                    
                    if (!empty($crop)) {
                        list($width, $height) = getimagesize($url);
                        Image::crop($url, 
                                floor($width * trim($crop[3]) - $width * trim($crop[1])), 
                                floor($height * trim($crop[2]) - $height * trim($crop[0])), 
                                [
                                    floor($width * trim($crop[1])), 
                                    floor($height * trim($crop[0]))
                                ])->save($url);
                    }                 
                    
                    list($width, $height) = getimagesize($url);
                    
                    return Json::encode([
                        'size' => [$width, $height],
                        'url'  => '/' . $url,
                        'alt'  => basename($url)
                    ]);
                }
                catch (Exception $e) {
                    return Json::encode(['errors' => [$e->getMessage()]]);
                }                    
            }
            else {
                return Json::encode(['errors' => ['Invalid insert options!']]);
            }
        }
    }
}