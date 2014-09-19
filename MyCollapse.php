<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 05.07.14
 * Time: 10:29
 */

namespace insolita\widgets;

use yii\bootstrap\Collapse;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class MyCollapse extends Collapse
{

    public $panelstyle = 'default';

    public function renderItems()
    {
        $items = [];
        $index = 0;
        foreach ($this->items as $header => $item) {
            $options = ArrayHelper::getValue($item, 'options', []);
            Html::addCssClass($options, 'panel panel-' . $this->panelstyle);
            $items[] = Html::tag('div', $this->renderItem($header, $item, ++$index), $options);
        }

        return implode("\n", $items);
    }
} 