<?php

namespace common\widgets;

class Tabs extends \yii\bootstrap\Widget
{

    public $items = [];


    public function init()
    {
        parent::init();
        $this->js();
    }

    public function run()
    {
        $out = '<div class="ptabs">';
        $out .= '<ul class="nav nav-tabs">';
        $tab_content = '';
        foreach ($this->items as $i => $item) {
            $active = (!empty($item['active'])) ? 'active' : '';
            $out .= '<li class="' . $active . '" data-show="' . $i . '"><a href="#">' . $item['label'] . '</a></li>';
            $tab_content .= '<div class="tab-pane ' . $active . '" data-content="' . $i . '">' . $item['content'] . '</div>';
        }
        $out .= '</ul>';
        $out .= '<div class="tab-content">';
        $out .= $tab_content;
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

    private function js()
    {
        $script = <<< JS
        var ptabs = function(obj) {
          $(obj).each(function() {
                var item_this = this;
                $(item_this).children('ul').children('li').each(function() {
                var show = $(this).data('show');
                $(this).click(function() {
                  $(item_this).children('ul').children('li').removeClass('active');  
                  $(this).addClass('active');  
                  $(item_this).children('.tab-content').children('.tab-pane').hide();
                  $(item_this).children('.tab-content').children('div[data-content="' + show + '"]').show();
                  return false;  
                });
            });
          });
        };
        
        ptabs('.ptabs');
JS;

        $this->view->registerJs($script, \yii\web\View::POS_READY);
    }
}
