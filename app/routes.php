<?php
declare(strict_types=1);

use App\Controllers\AccountsController;
use App\Controllers\FundsController;
use App\Controllers\InvestmentTransactionsController;
use Slim\App;
use Slim\Views\PhpRenderer;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $container = $app->getContainer();

    /* $app->get('/', function ($request, $response, $args) use ($container) {
        $renderer = $container->get(PhpRenderer::class);
        return $renderer->render($response, "index.php", $args);
    }); */

    $app->get('/test', function ($request, $response, $args) use ($container) {
        
        return $response->withJson();
    });

    // This route could be public
    $app->get('/funds', FundsController::class . ':index');

    // Routes to create, update and delete Funds must be restricted to Admins only (out of scope for this task)

    // Authenticated Routes
    $app->get('/accounts/investment/{account_id}/transactions', InvestmentTransactionsController::class . ':index');
    $app->post('/accounts/investment/{account_id}/transactions', InvestmentTransactionsController::class . ':store');
    $app->get('/accounts', AccountsController::class . ':index');

};
