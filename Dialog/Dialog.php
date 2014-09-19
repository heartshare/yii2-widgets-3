<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 09.07.14
 * Time: 23:47
 */

namespace insolita\widgets\Dialog;


use insolita\helpers\Helper;
use widgets\FormDialog\FormDialogAssets;
use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

class Dialog extends Widget
{
    /**
     * @var array $body_options - additional options for bootstrap modal body
     **/
    public $body_options = [];

    public $selector = '[data-dialoger]';

    public $showCloseButton = true;

    public $customButtons = null;

    /**
     * @var string $onHideEvent - custom event after load content -  override this widget`s default
     **/
    public $onHideEvent = 'function(dialog){}';


    /**@var string $style (one of - primary, success, info , default, danger)* */
    public $style = 'primary';

    /**@var string $size (one of - normal, large)* */
    public $size = 'normal';

    /**@var string $title - default modal title ** */
    public $title = 'Default title';

    public $content = 'Content';
    public $remote = false;


    private $js = [];


    public function init()
    {
        $this->registerModalScript();
        if (!$this->content && !$this->remote) {
            throw new InvalidConfigException('Необходимо указать контент');
        }
    }

    public function run()
    {

    }

    public function  registerModalScript()
    {
        $view = $this->getView();
        DialogAssets::register($view);
        $id = $this->options['id'];
        $buttons = [];
        if ($this->showCloseButton) {

            $buttons = [
                [
                    'icon' => 'fa fa-times-circle fa-lg',
                    'cssClass' => 'btn-danger pull-right',
                    'label' => 'Закрыть',
                    'hotkey' => 27,
                    'action' => new JsExpression("function(dialog){dialog.close();}")
                ]
            ];
        }

        if (!empty($this->customButtons) && is_array($this->customButtons)) {
            $buttons = ArrayHelper::merge($buttons, $this->customButtons);
        }
        $buttons = Json::encode($buttons);

        if ($this->content) {
            $this->js[]
                = <<<JS
               $(document).on('click','{$this->selector}',function(e){
                  thisDialog=  myStaticDialog('{$this->content}','size-{$this->size}','type-{$this->style}','$this->title',$buttons);
                  thisDialog.onhidden($this->onHideEvent);
               });
JS;
        } else {

            $this->js[]
                = <<<JS
                $(document).on('click','{$this->selector}',function(e){
                thisDialog= myBootstrapDialog('{$this->remote}','size-{$this->size}','type-{$this->style}','$this->title',$buttons,false,false);
                thisDialog.getModal().on('hidden.bs.modal',$this->onHideEvent);
               });
JS;

        }


        $view->registerJs(implode("\n", $this->js));
    }

} 