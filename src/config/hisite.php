<?php

/*
 * Identity and Access Management server providing OAuth2, RBAC and logging
 *
 * @link      https://github.com/hiqdev/hiam-core
 * @package   hiam-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

return [
    'id'            => 'hiam',
    'name'          => 'HIAM',
    'basePath'      => dirname(__DIR__),
    'viewPath'      => '@hisite/views',
    'vendorPath'    => '@root/vendor',
    'runtimePath'   => '@root/runtime',
    'bootstrap'     => ['log', 'urlManager', 'themeManager'],
    'defaultRoute'  => 'site',
    'layout'        => 'mini',
    'controllerNamespace' => 'hiam\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'log' => [
            'traceLevel' => defined('YII_DEBUG') && YII_DEBUG ? 3 : 0,
        ],
        'request' => [
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'db' => [
            'class'     => \yii\db\Connection::class,
            'dsn'       => 'pgsql:dbname=hiam',
            'charset'   => 'utf8',
            'dsn'       => 'pgsql:dbname=' . $params['db_name'],
            'username'  => $params['db_user'],
            'password'  => $params['db_password'],
        ],
        'user' => [
            'class'           => \yii\web\User::class,
            'identityClass'   => \hiam\models\User::class,
            'enableAutoLogin' => true,
        ],
        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            'viewPath' => '@hiam/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'authManager' => [
            'class'             => \hiam\rbac\HiDbManager::class,
            'itemTable'         => '{{%rbac_item}}',
            'itemChildTable'    => '{{%rbac_item_child}}',
            'assignmentTable'   => '{{%rbac_assignment}}',
            'ruleTable'         => '{{%rbac_rule}}',
        ],
        'authClientCollection' => [
            'class' => \hiam\authclient\Collection::class,
            'clients' => [
                'facebook' => [
                    'class'        => 'yii\authclient\clients\Facebook',
                    'clientId'     => $params['facebook_client_id'],
                    'clientSecret' => $params['facebook_client_secret'],
                ],
                'google' => [
                    'class'        => 'yii\authclient\clients\GoogleOAuth',
                    'clientId'     => $params['google_client_id'],
                    'clientSecret' => $params['google_client_secret'],
                    'normalizeUserAttributeMap' => [
                        'email'      => ['emails', 0, 'value'],
                        'first_name' => ['name', 'givenName'],
                        'last_name'  => ['name', 'familyName'],
                    ],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
        ],
        'view' => [
            'class' => \hiqdev\thememanager\View::class,
        ],
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'themeManager' => [
            'theme'  => 'adminlte',
            'assets' => [
                \hiam\assets\AppAsset::class,
                \hiqdev\assets\icheck\iCheckAsset::class,
            ],
        ],
        'menuManager' => [
            'class' => \hiqdev\menumanager\MenuManager::class,
            'items' => [
                'breadcrumbs' => [
                    'saveToView' => 'breadcrumbs',
                ],
            ],
        ],
    ],
    'modules' => [
        'oauth2' => [
            'class' => \filsh\yii2\oauth2server\Module::class,
            'options' => [
                'enforce_state'     => false,
                'access_lifetime'   => 3600 * 24,
            ],
            'storageMap' => [
                'user_credentials'  => \hiam\models\User::class,
            ],
            'grantTypes' => [
///             'client_credentials' => [
///                 'class' => \OAuth2\GrantType\ClientCredentials::class,
///                 'allow_public_clients' => false
///             ],
                'authorization_code' => [
                    'class' => \OAuth2\GrantType\AuthorizationCode::class,
                ],
                'user_credentials' => [
                    'class' => \OAuth2\GrantType\UserCredentials::class,
                ],
                'refresh_token' => [
                    'class' => \OAuth2\GrantType\RefreshToken::class,
                    'always_issue_new_refresh_token' => true,
                ],
            ],
        ],
    ],
];
