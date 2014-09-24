<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 09.07.14
 * Time: 23:47
 */

namespace insolita\widgets\Dialog;


use insolita\things\helpers\Helper;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

class FormDialog extends Widget
{
    /**
     * @var array $body_options - additional options for bootstrap modal body
     **/
    public $body_options = [];
    /**
     * @var string $autofocus - string selector for input for set autofocus
     **/
    public $autofocus = null;
    /**
     * @var string $selector -   bind modal.show event on click this selector and load
     *  url from its "data-link" option
     **/
    public $selector = '[data-modaler]';
    /**
     * @var boolean $showFooterButtons - show Save and Cancel Button in Footer// if parent option $footer is empty
     *                                  - you form must be without submit button id you set this option to true
     **/
    public $showFooterButtons = true;
    /**
     * @var boolean $showSaveMoreButton - show button allows save data and get new form for add new record without close modal// if  option $showFooterButtons==true
     **/
    public $showSaveMoreButton = true;
    /**
     * @var array $customButtons -   [ 'title'=>'Button title',
     *                                   'id'=>'Button id',
     *                                   'data-link'=>(optional url for load remote content)
     *                                   'onclick'=>javascript event  (if data-link setted - load content)
     *                                 ]
     **/
    public $customButtons = null;
    /**
     * @var array $hotKeys - hotKeys for raise modal(show) event
     **/
    public $hotKeys = ['Ctrl+1'];

    /**
     * @var string $onSubmitEvent - custom event on click save button -  override this widget`s default
     *                               required showFooterButtons - true
     * default widget`s event - bind on id of footer`s save button, posting form data via ajax to form action url, and if
     * recieve json response ==true hide modal, else show in error block response value
     *                            it must be setted as
     * @example
     *   new JsExpression("function(dialog){ your code here}")
     **/

    public $onSubmitEvent = null;

    /**
     * @var string $onCloseEvent - custom event on click cancel button -  override this widget`s default
     *                               required showFooterButtons - true
     *                               it must be setted as
     * @example
     *   new JsExpression("function(dialog){ your code here}")
     **/
    public $onCloseEvent = null;

    /**
     * @var string $onShownEvent - custom event after show dialog, it must be setted as
     * @example
     *   new JsExpression("function(dialog){ your code here}")
     *
     * !!!!Attention!!! it override autofocus
     **/
    public $onShownEvent = null;

    /**
     * @var string $pjaxSelector - if you have a grid or listview in pjax, set pjax selector
     **/
    public $pjaxSelector = null;

    /**@var string $style (one of - primary, success, info , default, danger)* */
    public $style = 'primary';

    /**@var string $size (one of - normal, large)* */
    public $size = 'normal';

    /**@var string $title - default modal title ** */
    public $title = 'Default title';

    public $forcemodal = false;

    private $js = [];

    /**@var bool $noajaxsubmit - if true, trigger form submit action, (only for default submit event, custom onSubmitEvent override this) ** */
    public $noajaxsubmit = false;


    public function init()
    {
        if (!$this->autofocus) {
            $this->autofocus = 'input[type="text"]:first';
        }
        $show=isset($this->getView()->context->_showtype)
               ?($this->getView()->context->_showtype == 'modal' || $this->forcemodal)
               :true;
        if ($show) {
            $this->registerModalScript();
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
        if($this->onShownEvent){
            $onshown=$this->onShownEvent;
        }else{
            $onshown = ($this->autofocus) ? new JsExpression("function(dialog){jQuery('{$this->autofocus}').focus();}")
                : new JsExpression("function(dialog){}");
        }
        $buttons = [];
        if ($this->showFooterButtons) {
            if(!$this->onSubmitEvent){
                if($this->noajaxsubmit){
                    $this->onSubmitEvent=new JsExpression("function(dialog){ $('#modalform').trigger('submit');}");
                }else{
                    $this->onSubmitEvent=($this->showSaveMoreButton)
                        ?new JsExpression("function(dialog){
                                    e.preventDefault();
                                    $('#resp_success').hide();$('#resp_error').hide();
                                    jQuery.post($('#modalform').attr('action'),
                                         $('#modalform').serialize(),
                                         function(resp){
                                           if(resp.state==true){
                                               jQuery('#resp_error').hide();jQuery('#resp_success').html('" . Helper::Fa('smile-o') . " OK! Добавляйте следующее!');
                                               $('#modalform').trigger('reset');
                                               jQuery('#resp_success').show();jQuery('#" . $id . " .modal-body').load(hkUrl_$id);
                                           }else{
                                               jQuery('#resp_success').hide(); jQuery('#resp_error').html(resp.error); jQuery('#resp_error').show();
                                           }
                                    });}")
                        :new JsExpression("function(dialog){
                                     $('#resp_success').hide();$('#resp_error').hide();
                                     jQuery.post($('#modalform').attr('action'),$('#modalform').serialize(),
                                           function(resp){
                                           if(resp.state==true){
                                                   dialog.close();
                                           }else{
                                               $('#resp_success').hide(); $('#resp_error').html(resp.error); $('#resp_error').show();
                                           }
                                     });}");
                }

            }

            if(!$this->onCloseEvent){
                $this->onCloseEvent=new JsExpression("function(dialog){dialog.close();}");
            }

            $buttons = [
                    [
                        'id' => $id . '_submit',
                        'icon' => 'fa fa-check-circle fa-lg',
                        'label' => 'Сохранить',
                        'cssClass' => 'btn-success pull-left',
                        'hotkey' => 13,
                        'action' => $this->onSubmitEvent
                    ],
                    [
                        'icon' => 'fa fa-times-circle fa-lg',
                        'cssClass' => 'btn-danger pull-right',
                        'label' => 'Закрыть',
                        'hotkey' => 27,
                        'action' => $this->onCloseEvent
                    ]
                ] ;
        }

        if (!empty($this->customButtons) && is_array($this->customButtons)) {
            $buttons = ArrayHelper::merge($buttons, $this->customButtons);
        }
        $buttons = Json::encode($buttons);
        $this->js[] = "var ishk_$id='" . (!empty($this->hotKeys) ? 1 : 0) . "';
               var ismore_$id='" . ($this->showSaveMoreButton ? 1 : 0) . "';
               var len_$id = $('{$this->selector}').length;
               if((len_$id>1) && (ishk_$id=='1' || ismore_$id=='1')){
                  alert('Некорректная конфигурация виджета на селектор {$this->selector} !!!');
               }
               if(ishk_$id=='1'){
                    var hkUrl_$id=jQuery('{$this->selector}').data('link');
               }";

        $js
            = <<<JS

               $(document).on('click','{$this->selector}',function(e){
                    e.preventDefault();
                    myBootstrapDialog($(this).data('link'),'size-{$this->size}','type-{$this->style}','$this->title',$buttons, $onshown, '{$this->pjaxSelector}');
               });
JS;

        $this->js[] = $js;
        if (!empty($this->hotKeys) && is_array($this->hotKeys) && strpos($id, 'add') !== false) {
            foreach ($this->hotKeys as $hotKey) {
                $this->js[] = "jQuery(document).bind('keypress','$hotKey' ,function(event){
                     myBootstrapDialog(hkUrl_$id,'size-$this->size','type-$this->style','{$this->title}',$buttons, $onshown, '{$this->pjaxSelector}');
                    console.log('show modal by hotkey');
               });";
            }

        }
        $view->registerJs(implode("\n", $this->js));
    }

} 