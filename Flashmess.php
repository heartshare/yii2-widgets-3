<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 07.07.14
 * Time: 22:44
 */

namespace insolita\widgets;


use yii\bootstrap\Widget;
use yii\helpers\Html;

class Flashmess extends Widget
{
    public $icon = '';
    public $title = '';
    public $message = '';
    public $type = '';

    public function run()
    {
        echo $this->getFlash();
    }

    protected function getFlash()
    {
        $tpl
            = '<div class="alert alert-{type}" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
            .
            ($this->title ? '<h4 class="text-{type}">{title}</h4><hr/>' : '') .
            '<span class="pull-left">{icon}</span> &nbsp;' .
            '{message}
           </div>';

        $vars = [
            '{type}' => $this->type,
            '{icon}' => $this->icon,
            '{title}' => $this->title,
            '{message}' => $this->message
        ];
        return strtr($tpl, $vars);
    }


} 