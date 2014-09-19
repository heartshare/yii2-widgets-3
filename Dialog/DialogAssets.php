<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 09.07.14
 * Time: 23:44
 */

namespace insolita\widgets\Dialog;


use yii\web\AssetBundle;

class DialogAssets extends AssetBundle
{
    public $sourcePath = '@vendor/insolita/yii2-widgets/Dialog/asset';
    public $js
        = [
            'jquery.hotkeys.js',
            'modaler.js'
        ];
    public $css
        = [
            'modaler.css'
        ];
    public $depends
        = [
            'yii\web\JqueryAsset',
        ];
}