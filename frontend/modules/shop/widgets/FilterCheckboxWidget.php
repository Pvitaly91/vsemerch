<?php

namespace frontend\modules\shop\widgets;

use common\models\ProductOption;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class FilterCheckboxWidget extends Widget
{
    public $productsQuery;
    public $url;
    public $item;

    private static $instance = [];

    public function init()
    {
        $this->js();
        parent::init();
    }

    public static function getListFilters($productsQuery, $item = [])
    {
        if ($_filters_str = Yii::$app->request->get('filters')) {
            $_filters_array = [];
            $_g_array = explode(';', $_filters_str);

            foreach ($_g_array as $group) {
                $group_array = explode(':', $group);
                if (count($group_array) != 2) {
                    continue;
                }
                $group_name = $group_array[0];
                $group_value = $group_array[1];
                $_f_array = explode(',', $group_value);
                $query = ProductOption::find()->alias('po');
                $query->join('JOIN', $productsQuery->tablesUsedInFrom, $productsQuery->tablesUsedInFrom['{{shop_product}}'] . '.id = po.product_id')
                    ->andWhere($productsQuery->where)
                    ->andWhere(['po.is_filter' => 1]);

                $query->andWhere(['po.slug_option' => $group_name]);
                if (!isset($item['slug_option']) || $group_name != $item['slug_option']) {
                    $query->leftJoin(productOption::tableName() . " `po_{$group_name}`", "po.product_id = `po_{$group_name}`.product_id")
                        ->onCondition(['in', "`po_{$group_name}`.slug", $_f_array])
                        ->andOnCondition(["`po_{$group_name}`.slug_option" => $group_name])
                        ->andOnCondition(["`po_{$group_name}`.is_filter" => 1]);

                    if (is_array($item) && count($item)) {
                        $query->leftJoin(productOption::tableName() . " `po_item`", "po.product_id = `po_item`.product_id")
                            ->onCondition(['in', "`po_item`.slug", $item['slug']])
                            ->andOnCondition(["`po_item`.slug_option" => $item['slug_option']])
                            ->andOnCondition(["`po_item`.is_filter" => 1]);
                    }
                }

                $productOptionModels = $query->groupBy('po.slug')->asArray()->all();

                foreach ($_f_array as $filter) {
                    foreach ($productOptionModels as $_item) {
                        if ($filter == $_item['slug'] && $group_name == $_item['slug_option']) {
                            $_filters_array[$_item['slug_option']][] = $_item['slug'];
                        }
                    }
                }

            }
            $filters_array = $_filters_array;
        } else {
            $filters_array = [];
        }

        return $filters_array;
    }

    public function run()
    {
        $filter_slug = $this->item['slug'];
        $group_slug = $this->item['slug_option'];
        $checked = false;

        if ($_filters_array = self::getListFilters($this->productsQuery, $this->item)) {
            if (isset($_filters_array[$group_slug]) && in_array($filter_slug, $_filters_array[$group_slug])) {
                $checked = true;
                unset($_filters_array[$group_slug][array_search($filter_slug, $_filters_array[$group_slug])]);
            } else {
                $_filters_array[$group_slug][] = $filter_slug;
            }
        } else {
            $_filters_array[$group_slug][] = $filter_slug;
        }

        $_group_array = [];
        foreach ($_filters_array as $group => $_filter) {
            if (!empty($_filter)) {
                asort($_filter);
                $_group_array[] = $group . ':' . implode(',', $_filter);
            }
        }

        asort($_group_array);

        $filters = ($_group_array) ? implode(';', $_group_array) : null;

        $url = array_merge($this->url, ['filters' => $filters]);
        if ($this->item['count_product']) {
            return Html::checkbox('input-first', $checked, ['class' => 'filter_checkbox checkradios']) . ' ' . Html::a($this->item['value'], rawurldecode(Url::to($url))) . ' ' . "<span>({$this->item['count_product']})</span>";
        } else {
          return '<span class="checkradios-checkbox checkbox-disabled"></span> ' . $this->item['value'] . ' ' . "({$this->item['count_product']})";
        }
        /*
        if ($this->item['count_product']) {
            return Html::checkbox('filter', $checked, ['class' => 'filter_checkbox checkradios']) . ' ' . Html::a($this->item['value'], rawurldecode(Url::to($url))) . ' ' . "({$this->item['count_product']})";
        } else {
            return '<span class="checkradios-checkbox checkbox-disabled"></span> ' . $this->item['value'] . ' ' . "({$this->item['count_product']})";
        }*/
    }

    private function js()
    {
        $script = <<< JS
        $('.filter_checkbox').change(function() {
              
          document.location = $(this).next('a').attr('href');
        });
JS;

        $this->view->registerJs($script, yii\web\View::POS_READY);
    }
}

?>