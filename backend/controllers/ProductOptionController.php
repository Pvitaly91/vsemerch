<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\ProductOption;
use common\models\ProductOptionTranslate;
use yii\web\HttpException;
use common\models\Product;
use common\models\Category;

class ProductOptionController extends Controller {

    function getProductOptionsIds($shop_product_option_id, $flag = false) {
        $partner = $catid = false;
        //get shop product option
        $productOption = ProductOption::find()->where(["id" => $shop_product_option_id])->one();

        //get product 
        $product = Product::find()
                        ->where(["id" => $productOption->product_id])
                        ->andWhere(["not_active" => 0])
                        ->select("id,category_id,partner")->one();
        $catid = $product->category_id;
        $partner = $product->partner;

        $allProducts = Product::find()
                // ->where(["category_id" => $catid])
                ->andWhere(["partner" => $partner])
                ->andWhere(["not_active" => 0])
                ->select("id");
        if ($flag == true)
            $allProducts = $allProducts->where(["category_id" => $catid]);

        $allProducts = $allProducts->all();

        return $allProducts;
    }

    function getOptionModelsByproductsModels($allProducts, $optionTranslate, $lng) {
        //   
        $models = [];
        \Yii::$app->language = $lng;

        foreach ($allProducts as $model) {

            foreach ($model->option as $option) {

                if ($option->translation->option == $optionTranslate) {

                    $models[] = $option->translation;
                }
            }
        }
        return $models;
    }

    function delAllTranslate() {

        $a = \metalguardian\i18n\models\SourceMessage::find([])->where(["category" => "partner_proprties_trans"])->all();
        foreach ($a as $it) {
            $mes = \metalguardian\i18n\models\SourceMessage::find([])->where(["id" => $it->id])->all();
            foreach ($mes as $m) {
                if ($m->delete()) {
                    $it->delete();
                }
            }
        }
    }
    function makeBaseTranslations(){
        $en = array(
            'material_of_the_bulb' => 'Material of the bulb',
            'housing_material' => 'Housing material',
            'weight_with_packing' => 'Weight with packing',
            'moq' => 'Moq',
            'diameter' => 'Diameter',
            'height' => 'Height',
            'freight_packing' => 'Freight packing',
            'weight' => 'Weight',
            'branding_type' => 'Branding type',
            'volume' => 'Volume',
            'color' => 'Color',
            'material' => 'Material',
            'individual_packaging' => 'Individual packaging',
            'wlength' => 'Wlength',
            'width' => 'Width',
            'cover' => 'Cover',
            'cover_material' => 'Cover material',
            'bottom_diameter' => 'Bottom diameter',
            'upper_diameter' => 'Upper diameter',
            'surface' => 'Surface',
            'store' => 'Store',
            'diameter_of_saucer' => 'Diameter of saucer',
            'battery' => 'Battery',
            'features' => 'Features',
            'packing_list' => 'Packing list',
            'input' => 'Input',
            'output_usb1' => 'Output usb1',
            'capacity' => 'Capacity',
            'size' => 'Size',
            'size_on' => 'Size on',
            'size_off' => 'Size off',
            'trademark' => 'Trademark',
            'density' => 'Density',
            'size_clothes' => 'Size clothes',
            'productId' => 'ProductId',
            'drive_type' => 'Drive type',
            'ink_color' => 'Ink color',
            'length_of_handle' => 'Length of handle',
            'pen_type' => 'Pen type',
            'clip_color' => 'Clip color',
            'basic_article' => 'Basic article',
            'number_of_pages' => 'Number of pages',
            'color_block' => 'Color block',
            'block_type' => 'Block type',
            'format' => 'Format',
            'model' => 'Model',
            'umbrella_type' => 'Umbrella type',
            'dimensions' => 'Dimensions',
            'pocket' => 'Pocket',
            'clip_size' => 'Clip size',
            'output_usb2' => 'Output usb2',
            'lead_color' => 'Lead color',
            'vid' => 'Vid',
            'country' => 'Country',
            'panton' => 'Panton',
            'gender' => 'Gender',
            'the_type_of_notebooks' => 'The type of notebooks',
            'printing' => 'Printing',
            'color_main' => 'Color main',
            'підкладка' => 'підкладка',
            'наповнювач' => 'наповнювач',
            'thickness' => 'Thickness',
            'sewing_method' => 'Sewing method',
            'handle_height_on_bag' => 'Handle height on bag',
            'handle_length' => 'Handle length',
            'carrying_capacity_' => 'Carrying capacity ',
            'composition' => 'Composition',
            'expiration_date' => 'Expiration date',
            'quantity' => 'Quantity',
        );
        $uk = [
            'material_of_the_bulb' => 'Матеріал лампочки',
            'housing_material' => 'Матеріал корпусу',
            'weight_with_packing' => 'Вага з упаковкою',
            'moq' => 'Мінімальне замовлення',
            'diameter' => 'Діаметр',
            'height' => 'Висота',
            'freight_packing' => 'Вантажна упаковка',
            'weight' => 'Вага',
            'branding_type' => 'Тип брендування',
            'volume' => 'Об\'єм',
            'color' => 'Колір',
            'material' => 'Матеріал',
            'individual_packaging' => 'Індивідуальна упаковка',
            'wlength' => 'Довжина хвилі',
            'width' => 'Ширина',
            'cover' => 'Обкладинка',
            'cover_material' => 'Матеріал обкладинки',
            'bottom_diameter' => 'Діаметр дна',
            'upper_diameter' => 'Діаметр верху',
            'surface' => 'Поверхня',
            'store' => 'Магазин',
            'diameter_of_saucer' => 'Діаметр блюдця',
            'battery' => 'Батарея',
            'features' => 'Особливості',
            'packing_list' => 'Список упаковки',
            'input' => 'Вхід',
            'output_usb1' => 'Вихід usb1',
            'capacity' => 'Ємність',
            'size' => 'Розмір',
            'size_on' => 'Розмір увімкнено',
            'size_off' => 'Розмір вимкнено',
            'trademark' => 'Товарний знак',
            'density' => 'Щільність',
            'size_clothes' => 'Розмір одягу',
            'productId' => 'Ідентифікатор продукту',
            'drive_type' => 'Тип приводу',
            'ink_color' => 'Колір чорнила',
            'length_of_handle' => 'Довжина ручки',
            'pen_type' => 'Тип ручки',
            'clip_color' => 'Колір клипа',
            'basic_article' => 'Основна стаття',
            'number_of_pages' => 'Кількість сторінок',
            'color_block' => 'Кольоровий блок',
            'block_type' => 'Тип блоку',
            'format' => 'Формат',
            'model' => 'Модель',
            'umbrella_type' => 'Тип парасольки',
            'dimensions' => 'Розміри',
            'pocket' => 'Кишеня',
            'clip_size' => 'Розмір кліпа',
            'output_usb2' => 'Вихід usb2',
            'lead_color' => 'Колір ведучого',
            'vid' => 'Вид',
            'country' => 'Країна',
            'panton' => 'Пантон',
            'gender' => 'Стать',
            'the_type_of_notebooks' => 'Тип записників',
            'printing' => 'Друк',
            'color_main' => 'Основний колір',
            'підкладка' => 'Підкладка',
            'наповнювач' => 'Наповнювач',
            'thickness' => 'Товщина',
            'sewing_method' => 'Метод шиття',
            'handle_height_on_bag' => 'Висота ручки на сумці',
            'handle_length' => 'Довжина ручки',
            'carrying_capacity_' => 'Грузопідйомність',
            'composition' => 'Склад',
            'expiration_date' => 'Термін придатності',
            'quantity' => 'Кількість',
        ];

        $ru = [
            'material_of_the_bulb' => 'Материал лампочки',
            'housing_material' => 'Материал корпуса',
            'weight_with_packing' => 'Вес с упаковкой',
            'moq' => 'Минимальный заказ',
            'diameter' => 'Диаметр',
            'height' => 'Высота',
            'freight_packing' => 'Грузовая упаковка',
            'weight' => 'Вес',
            'branding_type' => 'Тип брендирования',
            'volume' => 'Объем',
            'color' => 'Цвет',
            'material' => 'Материал',
            'individual_packaging' => 'Индивидуальная упаковка',
            'wlength' => 'Длина волны',
            'width' => 'Ширина',
            'cover' => 'Обложка',
            'cover_material' => 'Материал обложки',
            'bottom_diameter' => 'Диаметр дна',
            'upper_diameter' => 'Диаметр верха',
            'surface' => 'Поверхность',
            'store' => 'Магазин',
            'diameter_of_saucer' => 'Диаметр блюдца',
            'battery' => 'Батарея',
            'features' => 'Особенности',
            'packing_list' => 'Список упаковки',
            'input' => 'Вход',
            'output_usb1' => 'Выход usb1',
            'capacity' => 'Емкость',
            'size' => 'Размер',
            'size_on' => 'Размер включено',
            'size_off' => 'Размер выключено',
            'trademark' => 'Торговая марка',
            'density' => 'Плотность',
            'size_clothes' => 'Размер одежды',
            'productId' => 'Идентификатор продукта',
            'drive_type' => 'Тип привода',
            'ink_color' => 'Цвет чернил',
            'length_of_handle' => 'Длина ручки',
            'pen_type' => 'Тип ручки',
            'clip_color' => 'Цвет клипа',
            'basic_article' => 'Основная статья',
            'number_of_pages' => 'Количество страниц',
            'color_block' => 'Цветной блок',
            'block_type' => 'Тип блока',
            'format' => 'Формат',
            'model' => 'Модель',
            'umbrella_type' => 'Тип зонта',
            'dimensions' => 'Размеры',
            'pocket' => 'Карман',
            'clip_size' => 'Размер клипа',
            'output_usb2' => 'Выход usb2',
            'lead_color' => 'Цвет провода',
            'vid' => 'Вид',
            'country' => 'Страна',
            'panton' => 'Пантон',
            'gender' => 'Пол',
            'the_type_of_notebooks' => 'Тип блокнотов',
            'printing' => 'Печать',
            'color_main' => 'Основной цвет',
            'підкладка' => 'Подкладка',
            'наповнювач' => 'Наполнитель',
            'thickness' => 'Толщина',
            'sewing_method' => 'Метод шитья',
            'handle_height_on_bag' => 'Высота ручки на сумке',
            'handle_length' => 'Длина ручки',
            'carrying_capacity_' => 'Грузоподъемность',
            'composition' => 'Состав',
            'expiration_date' => 'Срок годности',
            'quantity' => 'Количество',
        ];
        

        
        foreach ($en as $key => $value) {
            $sr = new \metalguardian\i18n\models\SourceMessage();
            $sr->category = "partner_proprties_trans";
            $sr->message = $key;
            if ($sr->save(false)) {
                $mes = new \metalguardian\i18n\models\Message();
                $mes->id = $sr->id;
                $mes->language = "en";
                $mes->translation = $value;
                $mes->save(false);
                // ua
                if(isset($uk[$key])){
                    $mes = new \metalguardian\i18n\models\Message();
                    $mes->id = $sr->id;
                    $mes->language = "uk";
                    $mes->translation = $uk[$key];
                    $mes->save(false);
                }
                //ru
                 if(isset($ru[$key])){
                    $mes = new \metalguardian\i18n\models\Message();
                    $mes->id = $sr->id;
                    $mes->language = "ru";
                    $mes->translation = $ru[$key];
                    $mes->save(false);
                }
            }
        }
    }
    function getPartnerPropertiesList(){
         $slugs = [];
        $products = Product::find()
                        ->where(["partner" => "eney"])
                        ->andWhere(["not_active" => 0])
                        ->select("id,category_id,partner")->all();
        foreach ($products as $prod) {
            foreach ($prod->option as $option) {
                //  dd($option->translation->option);
                $parts = explode("_", $option->translation->option);
                $value = implode(" ", $parts);
                $slugs[$option->translation->option] = ucfirst($value);
            }
        }
        var_export($slugs);
    }
    
    function actionIndex($id, $lng) {
        
        if (!$model = ProductOptionTranslate::find()->where(["id" => $id])->one())
            throw new HttpException(404, 'Данной странице не существует!');



        if (($post = Yii::$app->request->post("ProductOptionTranslate")) == true) {
            $optionTranslate = $model->option;

            $flag = (isset($post["flag"]) && $post["flag"] > 0) ? true : false;

            $allProducts = $this->getProductOptionsIds($model->shop_product_option_id, $flag);

            $models = $this->getOptionModelsByproductsModels($allProducts, $optionTranslate, $lng);

            foreach ($models as $optionTranslateModel) {
                $optionTranslateModel->option = $post["option"];
                $optionTranslateModel->save(false);
            }
        }
        return $this->render('index', ['modelOption' => $model, "lng" => $lng]);
    }
}
