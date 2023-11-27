<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

date_default_timezone_set("America/Argentina/Buenos_Aires");

require __DIR__ . '/../vendor/autoload.php';
require './controllers/CuentaController.php';
require './controllers/DepositoController.php';
require './controllers/RetiroController.php';
require './controllers/AjusteController.php';
require_once './middlewares/AuthMiddleware.php';

define("AUTHTOKEN", \AuthMiddleware::class . ':verificarToken');

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido al Banco"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->group('/cuentas', function (RouteCollectorProxy $group) {
    $group->post('[/]', \CuentaController::class . ':AltaCuenta');
    $group->post('/consultarCuenta', \CuentaController::class . ':ConsultarCuenta');
    $group->post('/modificar', \CuentaController::class . ':ModificarCuenta');
    $group->post('/importar', \CuentaController::class . ':ImportarCuentas');
    $group->get('/exportar', \CuentaController::class . ':ExportarCuentas');
    $group->delete('[/]', \CuentaController::class . ':BorrarCuenta');
});

$app->group('/depositos', function (RouteCollectorProxy $group) {
    $group->post('[/]', \DepositoController::class . ':DepositoCuenta');
    $group->get('/consultaA', \DepositoController::class . ':Consulta1');
    $group->get('/consultaB', \DepositoController::class . ':Consulta2');
    $group->get('/consultaC', \DepositoController::class . ':Consulta3');
    $group->get('/consultaD', \DepositoController::class . ':Consulta4');
})->add(AUTHTOKEN);

$app->group('/retiros', function (RouteCollectorProxy $group) {
    $group->post('[/]', \RetiroController::class . ':RetiroCuenta');
    $group->get('/consultaA', \RetiroController::class . ':Consulta1');
    $group->get('/consultaB', \RetiroController::class . ':Consulta2');
    $group->get('/consultaC', \RetiroController::class . ':Consulta3');
    $group->get('/consultaD', \RetiroController::class . ':Consulta4');
})->add(AUTHTOKEN);

$app->group('/ajustes', function (RouteCollectorProxy $group) {
    $group->post('/deposito', \AjusteController::class . ':AjustarDeposito');
    $group->post('/retiro', \AjusteController::class . ':AjustarRetiro');
});

$app->group('/auth', function (RouteCollectorProxy $group) {

    $group->post('/login', function (Request $request, Response $response) {    
    $parametros = $request->getParsedBody();
    $usuario = $parametros['usuario'];
    $contraseña = $parametros['contraseña'];

    if($usuario == 'prueba' && $contraseña == '1234'){
        $datos = array('usuario' => $usuario);

        $token = AutentificadorJWT::CrearToken($datos);
        $payload = json_encode(array('jwt' => $token));
    } else {
        $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
    });
});

// $app->group('/auth', function (RouteCollectorProxy $group) {
//     $group->post('/login', function (Request $request, Response $response) {    
//     $parametros = $request->getParsedBody();

//     $usuario = $parametros['usuario'];
//     $rol = $parametros['rol'];

//     if(DbController::AuthLogin($usuario, $rol)){
//         if(DbController::AuthSuspendido($usuario, $rol)){
//             $datos = array('usuario' => $usuario, 'rol' => $rol);
//             $token = AutentificadorJWT::CrearToken($datos);
//             $payload = json_encode(array('jwt' => $token));
//         }else {
//             $payload = json_encode(array('error' => 'El usuario esta suspendido.'));
//             }
//     } else {
//     $payload = json_encode(array('error' => 'El usuario no existe.'));
//     }

//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
//     });
// });

$app->run();
