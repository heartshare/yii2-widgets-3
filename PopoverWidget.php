<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 08.06.14
 * Time: 22:38
 */

namespace insolita\widgets;

use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Widget;
use yii\helpers\Json;

/**
 ** Register popover bootstrap script
 * usage
 *
 * echo \widgets\PopoverWidget::widget([
 *  'selector'=>'#mypop',
 *  'clientOptions'=>['placement'=>'right']
 *  ])
 *
 **/
class PopoverWidget extends Widget
{

    /**
     * @var string $selector
     **/

    public $selector = '.pop';

    public function run()
    {
        $view = $this->getView();

        BootstrapPluginAsset::register($view);

        $id = $this->options['id'];

        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
            $js = "jQuery('{$this->selector}').popover($options);";
            $view->registerJs($js);
        }

        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('{$this->selector}').on('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }
    }
} 