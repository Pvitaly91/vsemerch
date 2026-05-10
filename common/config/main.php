<?php

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
        'i18n' => [
            'class' => '\common\components\I18n',
            'only' => [
                'app',
                'shop',
                'shop/frontend',
                'metalguardian/i18n',
            ],
            'override' => true,
        ],
        'xmlImport' =>[
            'class' => '\common\components\xmlImport',
        ],
        'PartnersUtils' => [
            'class' => '\common\components\PartnersUtils',
        ]
    ],
];
