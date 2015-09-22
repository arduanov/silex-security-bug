<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use Silex\Application;
use Silex\Provider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app['root.path'] = realpath(__DIR__ . "/../..");

$app->register(new Provider\SecurityServiceProvider());

$app->register(new Provider\RoutingServiceProvider());

$app->register(new Provider\ValidatorServiceProvider());
$app->register(new Provider\LocaleServiceProvider());
$app->register(new Provider\TranslationServiceProvider());
$app->register(new Provider\CsrfServiceProvider());
$app->register(new Provider\FormServiceProvider());

$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\TwigServiceProvider(), [
    'twig.path' => $app['root.path'] . '/templates/',
    'twig.form.templates' => ['bootstrap_3_horizontal_layout.html.twig']
]);


$app->get('/admin/post/add/', function () {
    return 'logged in!';
})->method('get|post')->bind('post_add');

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.twig', [
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});


$app['security.firewalls'] = [
    'secured_area' => [
        'pattern' => '^/admin',
        'anonymous' => false,
        'form' => ['login_path' => '/login', 'check_path' => '/admin/check_login',
            'always_use_default_target_path' => true,
            'default_target_path' => 'post_add',
        ],
        'users' => [
            'admin' => ['ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='],
        ],
    ],
];

return $app;