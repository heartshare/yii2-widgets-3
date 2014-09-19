<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 07.07.14
 * Time: 22:25
 */

namespace insolita\widgets;


use insolita\helpers\Helper;
use yii\bootstrap\Widget;

class Alerts extends Widget
{
    private $_classes;
    private $_keyparts;
    private $_icons;
    private $_titles;

    public function init()
    {
        $this->_classes = ['success' => 'success', 'error' => 'danger', 'info' => 'info', 'warning' => 'warning'];
        $this->_keyparts = array_keys($this->_classes);
        $this->_icons = [
            'success' => Helper::Fa('smile-o', 'lg'),
            'error' => Helper::Fa('frown-o', 'lg'),
            'info' => Helper::Fa('info-circle', 'lg'),
            'warning' => Helper::Fa('volume-up', 'lg')
        ];
        $this->_titles = [
            'success' => 'Успешно!',
            'error' => 'Ошибка!',
            'info' => 'К сведению!',
            'warning' => 'Внимание!'
        ];
    }

    public function run()
    {
        $allflash = \Yii::$app->session->getAllFlashes();
        $msg = '';
        foreach ($allflash as $key => $mess) {
            $fk = 'info';
            foreach ($this->_keyparts as $kp) {
                if (strpos($key, $kp) !== false) {
                    $fk = $kp;
                    break;
                }
            }
            $tpl
                = '<div class="alert alert-{type}" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'
                .
                // ($this->title?'<h4 class="text-{type}">{title}</h4><hr/>':'').
                '<span class="pull-left">{icon}</span> &nbsp;' .
                '{message}
               </div>';

            $vars = [
                '{type}' => $this->_classes[$fk],
                '{icon}' => $this->_icons[$fk],
                '{title}' => '',
                '{message}' => $mess
            ];
            $msg .= strtr($tpl, $vars);
        }
        return $msg;
    }
} 