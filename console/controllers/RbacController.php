<?php
namespace console\controllers;

use common\models\User;
use common\rbac\OwnModelRule;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();


        $admin = $auth->createRole('admin');
        $auth->add($admin);
       // $auth->addChild($admin, $manager);

        $auth->assign($admin, 1);
       // $auth->assign($manager, 2);
        //$auth->assign($user, 3);

        Console::output('Success! RBAC roles has been added.');
    }
}
