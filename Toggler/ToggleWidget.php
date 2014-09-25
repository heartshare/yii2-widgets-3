<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.09.14
 * Time: 22:56
 */
namespace insolita\widgets\Toggler;
use yii\bootstrap\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
class ToggleWidget extends Widget{

    public $on=1;

    public $off=0;

    public $onHtml='<i class="fa fa-square"></i>';

    public $offHtml='<i class="fa fa-square-o"></i>';

    public $callback='function(){}';

    public $url=null;

    public $value;

    public function init(){
        if(!$this->url || !$this->offHtml || !$this->onHtml || !in_array($this->value,[$this->on, $this->off])){
            throw new InvalidConfigException("Wrong settings");
        }
        parent::init();
        if(!$this->id && !isset($this->options['id'])){
            $this->id=$this->options['id']='toggler_'.$this->getId();
        }
        $this->registerJs();
    }

    public function run(){
        $txt=($this->value==$this->on)?$this->onHtml:$this->offHtml;
        echo Html::a($txt,$this->url,$this->options);
    }

    public function registerJs(){
        $view=$this->getView();
        ToggleAsset::register($view);
        $selector = "#{$this->id}";
        $callback = $this->callback ? : 'function(){}';
        $js[] = "sollyscript.toggler.onHtml='{$this->onHtml}'";
        $js[] = "sollyscript.toggler.offHtml='{$this->offHtml}'";
        $js[] = "sollyscript.toggler.registerHandler('$selector', $callback);";
        $view->registerJs(implode("\n", $js));
    }

}