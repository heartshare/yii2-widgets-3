<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.09.14
 * Time: 22:57
 */

class ToggleAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@vendor/insolita/yii2-widgets/Toggler';

    public $js = ['js/toggler.js'];

    public $depends = [
        'yii\web\YiiAsset'
    ];
} 