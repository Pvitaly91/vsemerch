<?php
namespace frontend\controllers;

use common\models\Product;
use frontend\modules\shop\controllers\traitMakeSku;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class DebugController extends Controller
{
    use traitMakeSku;

    private $allowedPageSizes = [12, 24, 48, 96];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['partner-products'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Yii::$app->request->getUrl());
                        return $this->redirect('/login');
                    }

                    throw new ForbiddenHttpException('Access denied.');
                },
            ],
        ];
    }

    public function actionPartnerProducts()
    {
        if (!$this->isAllowedUser()) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $partners = $this->getPartners();
        $partner = Yii::$app->request->get('partner', 'totobi');
        if (!isset($partners[$partner])) {
            $partner = key($partners);
        }

        $pageSize = (int)Yii::$app->request->get('per-page', 24);
        if (!in_array($pageSize, $this->allowedPageSizes, true)) {
            $pageSize = 24;
        }

        $query = Product::find()
            ->where(['partner' => $partner])
            ->orderBy([
                'not_available' => SORT_ASC,
                'id' => SORT_DESC,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                'pageSizeParam' => false,
                'forcePageParam' => false,
            ],
        ]);

        $products = $dataProvider->getModels();
        $colors = [];

        foreach ($products as $model) {
            $canonical = false;
            $colors[$model->id] = $this->makeSKU($model, $canonical, true) ?: [];
            $model->checkAvaiableSize();
        }

        return $this->render('partner-products', [
            'partners' => $partners,
            'partner' => $partner,
            'pageSize' => $pageSize,
            'pageSizes' => $this->allowedPageSizes,
            'dataProvider' => $dataProvider,
            'products' => $products,
            'colors' => $colors,
        ]);
    }

    private function isAllowedUser()
    {
        return !Yii::$app->user->isGuest
            && Yii::$app->user->identity
            && Yii::$app->user->identity->username === 'pavelagcity';
    }

    private function getPartners()
    {
        $partners = Product::find()
            ->select('partner')
            ->where(['not', ['partner' => null]])
            ->andWhere(['<>', 'partner', ''])
            ->groupBy('partner')
            ->orderBy('partner')
            ->column();

        $partners = ArrayHelper::map($partners, function ($partner) {
            return $partner;
        }, function ($partner) {
            return $partner;
        });

        if (!$partners) {
            return ['totobi' => 'totobi'];
        }

        return $partners;
    }
}
