<?php

namespace console\controllers;

use frontend\models\Catalog;
use frontend\models\News;
use frontend\models\Text;
use Yii;
use yii\console\Controller;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;

Class SitemapController extends Controller
{
    public function actionIndex()
    {
        $sitemap = new Sitemap(Yii::getAlias('@frontend/web/sitemap.xml'));

        $sitemap->addItem('https://agcity.com.ua', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/text/about', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/design', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/web', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/production', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/catalog', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/production/naruzhnaya_reklama', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/contacts', time(), Sitemap::HOURLY, 1.0);

        $sitemap->addItem('https://agcity.com.ua/news', time(), Sitemap::HOURLY, 1.0);

        foreach (Catalog::find()->all() as $item) {
            $sitemap->addItem('https://agcity.com.ua/catalog/' . $item->translit, time(), Sitemap::DAILY, 0.9);
        }

        foreach (News::find()->all() as $item) {
           // $sitemap->addItem('https://agcity.com.ua/news/' . $item->translit . '-'. $item->id, time(), Sitemap::DAILY, 0.9);
        }

        $sitemap->write();

    }
}