<?php
ob_start();
ini_set("display_errors", 1);
ini_set("display_starup_error", 1);
error_reporting(E_ALL);

require_once __DIR__."/../vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/..");
$dotenv->load();

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => $_ENV["DB_MOTOR"],
    'host'      => $_ENV["DB_HOST"],
    'database'  => $_ENV["DB_NAME"],
    'username'  => $_ENV["DB_USER"],
    'password'  => $_ENV["DB_PASSWORD"],
    'charset'   => $_ENV["DB_CHARSET"],
    'collation' => $_ENV["DB_CHARSET_COLLECTION"],
    'prefix'    => $_ENV["DB_PREFIX"],
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();


// crea un request en base a las globales
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);


$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get("index", "/", [
    "controller"=>"App\Controllers\IndexController",
    "action"=>"indexAction"
]);
$map->get("addNota", "/notas/add", [
    "controller"=>"App\Controllers\NotaController",
    "action"=>"addNota"
]);
$map->post("storeNota", "/notas/add", [
    "controller"=>"App\Controllers\NotaController",
    "action"=>"addNota"
]);
$map->get("userAdd", "/users/add", [
    "controller"=>"App\Controllers\UserController",
    "action"=>"getAddUser"
]);
$map->post("userStore", "/users/add", [
    "controller"=>"App\Controllers\UserController",
    "action"=>"postSaveUser"
]);
$map->get("login", "/login", [
    "controller"=>"App\Controllers\AuthController",
    "action"=>"doLogin"
]);
$map->post("auth", "/auth", [
    "controller"=>"App\Controllers\AuthController",
    "action"=>"postLogin"
]);
$map->get("admin", "/admin", [
    "controller"=>"App\Controllers\AdminController",
    "action"=>"getIndex",
    "auth" => true
]);

$map->get("logout", "/auth/logout", [
    "controller"=>"App\Controllers\AuthController",
    "action"=>"getLogout"
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if(!$route){
    echo "No hay ruta";
    return false;
    die();
} 

$handlerData = $route->handler; 
$controllerName = $handlerData["controller"];
$actionName = $handlerData["action"];


$needsAuth = $handlerData["auth"] ?? false;
$session_id = $_SESSION["userId"] ?? null;

if($needsAuth && !$session_id){
    echo "Ruta protegida";
    die;
}

$controller = new $controllerName;
$response = $controller->$actionName($request);

$headers = $response->getHeaders();

var_dump($headers);

foreach($headers as $name => $values){
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
http_response_code($response->getStatusCode());
echo $response->getBody();

ob_end_flush();