<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.09.14
 * Time: 22:57
 */
namespace insolita\widgets\Toggler;

use yii\web\AssetBundle;

class ToggleAsset extends AssetBundle {
    public $sourcePath = '@vendor/insolita/yii2-widgets/Toggler';

    public $js = ['js/toggler.js'];

    public $depends = [
        'yii\web\YiiAsset'
    ];
} 