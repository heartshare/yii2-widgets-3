<?php
/**
 * Created by PhpStorm.
 * User: Insolita
 * Date: 21.08.14
 * Time: 11:12
 */

namespace insolita\widgets;


use kartik\nav\NavX;
use yii\bootstrap\Nav;
use yii\bootstrap\Widget;
use \insolita\things\helpers\Helper;
use yii\caching\DbDependency;
use yii\helpers\Url;

class TopMenu extends Widget
{
    /**@var \insolita\menu\models\Menu[] $menudata * */
    public $menudata;
    /**@var \insolita\menu\models\Menu $modelClass * */
    public $modelClass;
    /**@var array $linkOptions * */
    public $linkOptions = [];

    /**@var array $toplinkOptions * */
    public $toplinkOptions = [];

    public $showtopicon = false;
    public $showallicon = true;

    public $navbar = false;
    public $baseurl = null;

    private $_menu = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $cd = new DbDependency();
        $model = $this->modelClass;
        $cd->sql = 'SELECT MAX(updated) FROM ' . $model::tableName();
        $cd->reusable = true;
        $this->_menu = \Yii::$app->db->cache(
            function ($db) { return $this->menuRender(); },
            86400,
            $cd
        );
        return (!$this->navbar)
            ? NavX::widget(
                [
                    'options' => ['class' => 'navbar-nav'],
                    'items' => $this->_menu,
                    'activateParents' => true,
                    'encodeLabels' => false
                ]
            )
            : Nav::widget(
                [
                    'encodeLabels' => false,
                    'options' => ['class' => 'navbar-nav navbar-left'],
                    'items' => $this->_menu,
                ]
            );
    }

    public function menuRender()
    {
        $model = $this->modelClass;
        if (!empty($this->menudata) && is_array($this->menudata)) {
            /**@var \insolita\menu\models\Menu $item * */
            foreach ($this->menudata as $item) {
                if ($model::checkPerm($item->visexpr)) {
                    $link = (mb_strpos($item->url, 'http', null, \Yii::$app->charset) !== false
                        or mb_strpos($item->url, 'html', null, \Yii::$app->charset) !== false
                        or $item->url == '#')
                        ? $item->url : $this->baseurl . Url::toRoute([$item->url]);
                    $this->_menu[$item->id] = [
                        'label' => ($this->showtopicon ? Helper::FA($item->icon) : '') . $item->name,
                        'url' => $link,
                        'linkOptions' => $this->toplinkOptions
                    ];
                    if ($item->childs) {
                        //unset($this->_menu[$item->id]['url']);
                        /**@var \insolita\menu\models\Menu $child * */
                        foreach ($item->childs as $child) {
                            if($child->active){
                                $ch2 = $model::checkPerm($child->visexpr);
                                if ($ch2) {
                                    $link = (mb_strpos($child->url, 'http', null, \Yii::$app->charset) !== false
                                        or mb_strpos($child->url, 'html', null, \Yii::$app->charset) !== false
                                        or $child->url == '#')
                                        ? $child->url : $this->baseurl . Url::toRoute([$child->url]);
                                    $this->_menu[$item->id]['items'][$child->id] = [
                                        'label' => ($this->showallicon ? Helper::FA($child->icon) : '') . $child->name,
                                        'url' => $link,
                                        'linkOptions' => $this->linkOptions,
                                    ];
                                    if ($child->childs) {
                                        /**@var \insolita\menu\models\Menu $subchild * */
                                        foreach ($child->childs as $subchild) {
                                            if($subchild->active){
                                                $ch3 = $model::checkPerm($subchild->visexpr);
                                                if ($ch3) {
                                                    $link = (mb_strpos(
                                                            $subchild->url, 'http', null, \Yii::$app->charset
                                                        ) !== false
                                                        or mb_strpos($subchild->url, 'html', null, \Yii::$app->charset)
                                                        !== false
                                                        or $subchild->url == '#')
                                                        ? $subchild->url : $this->baseurl . Url::toRoute([$subchild->url]);
                                                    $this->_menu[$item->id]['items'][$child->id]['items'][$subchild->id] = [
                                                        'label' => ($this->showallicon ? Helper::FA($subchild->icon) : "")
                                                            . $subchild->name,
                                                        'url' => $link,
                                                        'linkOptions' => $this->linkOptions,
                                                    ];
                                                }
                                            }

                                        }
                                    }
                                }
                            }

                        }

                    }
                }
            }
        }
        return $this->_menu;
    }

} 