<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 24.09.14
 * Time: 22:56
 */

class ToggleWidget extends \yii\bootstrap\Widget{

    public $on=1;

    public $off=0;

    public $onHtml='<i class="fa fa-square"></i>';

    public $offHtml='<i class="fa fa-square-o"></i>';

    public $callback='function(){}';

    public $url=['toggle'];

    public $elem;

    public function init(){
        if(!$this->url || !$this->offHtml || !$this->onHtml || $this->on || $this->off){
            throw new \yii\base\InvalidConfigException("Wrong settings");
        }
        parent::init();
        if(!$this->id && !isset($this->options['id'])){
            $this->id=$this->options['id']='toggler_'.$this->getId();
        }
        $this->registerJs();
    }

    public function run(){
        $txt=($this->elem==$this->on)?$this->onHtml:$this->offHtml;
        echo \yii\helpers\Html::a($txt,$this->url,[]);
    }

    public function registerJs(){
        $view=$this->getView();
        $selector = "#{$this->id}";
        $callback = $this->callback ? : 'function(){}';
        $js[] = "sollyscript.toggler.onHtml='{$this->onHtml}'";
        $js[] = "sollyscript.toggler.offHtml='{$this->offHtml}'";
        $js[] = "sollyscript.toggler.registerHandler('$selector', $callback);";
    }

}