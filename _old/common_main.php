<?php

/*
 * Identity and Access Management server providing OAuth2, RBAC and logging
 *
 * @link      https://github.com/hiqdev/hiam-core
 * @package   hiam-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

function d($a)
{
    echo '<pre>';
    die(var_dump($a));
}

$params = array_merge(
    require(Yii::getAlias('@hiam/common/config/params.php')),
    require(Yii::getAlias('@project/common/config/params.php')),
    require(Yii::getAlias('@project/common/config/params-local.php'))
);

$config = [
    'vendorPath' => '@project/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class'         => 'yii\db\Connection',
            'dsn'           => 'pgsql:dbname=' . $params['db_name'],
            'charset'       => 'utf8',
            'username'      => $params['db_user'],
            'password'      => $params['db_password'],
        ],
        'user' => [
            'class'           => 'yii\web\User',
            'identityClass'   => 'hiam\common\models\User',
            'enableAutoLogin' => true,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'authManager' => [
            'class'             => 'hiam\common\rbac\HiDbManager',
            'itemTable'         => '{{%rbac_item}}',
            'itemChildTable'    => '{{%rbac_item_child}}',
            'assignmentTable'   => '{{%rbac_assignment}}',
            'ruleTable'         => '{{%rbac_rule}}',
        ],
    ],
];

if (YII_DEBUG && !YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => $params['debug_ips'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
