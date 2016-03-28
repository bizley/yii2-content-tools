<?php

namespace bizley\contenttools\actions;

use Exception;
use Yii;
use yii\base\Action;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\imagine\Image;

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
 * 
 * Example action prepared for the Yii 2 ContentTools.
 * 
 * This action handles rotating of the image.
 * 
 * 'direction' parameter can be:
 * - 'CW' for clockwise rotation,
 * - 'CCW' for counterclockwise rotation.
 * 
 * Rotation is handled by the Imagine library through yii2-imagine extension.
 * JS engine can add '?_ignore=...' part to the url so it should be removed.
 * Action returns the size and url of rotated image.
 */
class RotateAction extends Action
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        try {
            if (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post();
                if (empty($data['url']) || !in_array($data['direction'], ['CW', 'CCW'])) {
                    throw new InvalidParamException('Invalid rotate options!');
                }
                    
                $url = trim($data['url']);
                if (substr($url, 0, 1) == '/') {
                    $url = substr($url, 1);
                }
                if (strpos($url, '?_ignore=') !== false) {
                    $url = substr($url, 0, strpos($url, '?_ignore='));
                }
                
                Image::getImagine()->open($url)->copy()->rotate($data['direction'] == 'CW' ? 90 : -90)->save($url);
                
                list($width, $height) = getimagesize($url);
                
                return Json::encode([
                    'size' => [$width, $height],
                    'url'  => '/' . $url
                ]);
            }
        } catch (Exception $e) {
           return Json::encode(['errors' => [$e->getMessage()]]);
        }
    }
}