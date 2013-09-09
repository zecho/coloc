<?php

use \Symfony\Component\HttpFoundation\Request;

$app = require __DIR__ . '/bootstrap.php';

$app['users'] = $app->share(function() use ($app) {
    $users = [];

    $persons = $app['db']->getMapFor('\Model\Person')
        ->findAll();
    foreach ($persons as $person) {
        $users[$person->email] = [
            'ROLE_ADMIN',
            $person->password,
        ];
    }
    return $users;
});

$app['security.firewalls'] = [
    'dev' => [
        'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
    ],
    'login' => [
        'pattern' => '^/login$',
        'anonymous' => true,
    ],
    'default' => [
        'pattern' => '^.*$',
        'form' => ['login_path' => '/login', 'check_path' => '/admin/login_check'],
        'logout' => ['logout_path' => '/logout'],
        'users' => $app['users'],
    ],
];

$app->get('/login', function(Request $request) use($app) {
    return $app['twig']->render('login.html.twig', [
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ]);
});

$app->get('/', function(Request $request) use($app) {
    $limit = $request->get('limit', 20);
    $page = $request->get('page', 1);

    $pager = $app['db']->getMapFor('\Model\Expense')
        ->paginateFindWhere('payment_id IS NULL', [], null, $limit, $page);
    return $app['twig']->render(
        'index.html.twig',
        compact('pager', 'limit')
    );
});

$app->get('/expenses', function(Request $request) use($app) {
    $limit = $request->get('limit', 20);
    $page = $request->get('page', 1);

    $pager = $app['db']->getMapFor('\Model\Expense')
        ->paginateFindWhere('1 = 1', [], null, $limit, $page);
    return $app['twig']->render(
        'index.html.twig',
        compact('pager', 'limit')
    );
});

$app->get('/expenses/add', function() use($app) {
    $persons = $app['db']->getMapFor('\Model\Person')
        ->findAll();

    return $app['twig']->render(
        'expense/add.html.twig',
        compact('persons')
    );
});

$app->post('/expenses/add', function(Request $request) use($app) {
    $map = $app['db']->getMapFor('\Model\Expense');

    $expense = $map->createObject();
    $expense->hydrate($request->request->get('expense'));
    $map->saveOne($expense);

    $app['session']->getFlashBag()
        ->add('success', 'Payement ajouté');
    return $app->redirect('/');
});

return $app;
