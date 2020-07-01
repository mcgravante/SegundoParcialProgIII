<?php

namespace Config;

use Slim\Routing\RouteCollectorProxy;
//Controllers
use App\Controllers\UsersController;
use App\Controllers\MateriasController;
use App\Controllers\TurnosController;
//middles
use App\Middleware\BeforeMiddleware;
use App\Middleware\UsuarioValidateMiddleware;
use App\Middleware\RegistroMiddleware;
use App\Middleware\LoginMiddleware;
use App\Middleware\RegistroMateriasMiddleware;
use App\Middleware\RegistroTurnoMiddleware;
use App\Middleware\AfterMiddleware;
use App\Middleware\TurnosVeterinariosMiddleware;
use App\Middleware\GetMateriasByIdMiddleware;
use App\Middleware\AsignoProfesorMiddleware;



return function ($app) 
{
    $app->post('/usuario',UsersController::class . ':add')->add(RegistroMiddleware::class);
    $app->post('/login',UsersController::class . ':login')->add(LoginMiddleware::class);
    $app->post('/materias',MateriasController::class . ':add')->add(RegistroMateriasMiddleware::class);
    $app->get('/materias/{id}',MateriasController::class . ':getById')->add(GetMateriasByIdMiddleware::class);
    $app->put('/materias/{id}/{profesor}',UsersController::class . ':addProfesorToMateria')->add(AsignoProfesorMiddleware::class);
   
};