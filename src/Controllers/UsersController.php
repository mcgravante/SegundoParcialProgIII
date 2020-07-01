<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use App\Models\Materia;

use \Firebase\JWT\JWT;


class UsersController 
{

    public function add(Request $request, Response $response, $args)
    {
            $user = new User;
            $requestBody = $request->getParsedBody();

            $requestEmail = $requestBody['email'];
            $requestClave = $requestBody['clave'];
            $requestTipo = $requestBody['tipo'];
            $requestNombre = $requestBody['nombre'];
            $requestLegajo = $requestBody['legajo'];

            $response->withHeader('Content-type', 'application/json');
            $user->email = $requestEmail;
            $user->clave = $requestClave;
            $user->tipo_id = $requestTipo;
            $user->nombre = $requestNombre;
            $user->legajo = $requestLegajo;
            $rta = json_encode(array("ok" => $user->save()));
            $response->getBody()->write($rta);
        
        return $response;
    }

    public function login(Request $request, Response $response, $args)
    {
        
        $requestBody = $request->getParsedBody();

        $requestEmail = $requestBody['email'];
        $requestClave = $requestBody['clave'];

        $user = json_decode(User::whereRaw('email = ? AND clave = ?',array($requestEmail,$requestClave))->get());

        $key = 'usuario';
        $payload = array(
            "email" => $user[0]->email,
            "clave" => $user[0]->clave,
            "tipo_id" => $user[0]->tipo_id,
            "legajo" => $user[0]->legajo,
            "nombre" =>$user[0]->nombre);

        $response->getBody()->write(JWT::encode($payload,$key));
        return $response->withHeader('Content-type', 'application/json');;
    }

    public function addProfesorToMateria(Request $request, Response $response, $args)
    {
        $affected = Materia::where('id', $args['id'])
        ->update(['profesor_id' => $args['profesor']]);

        $rta = json_encode($affected);
        $response->getBody()->write($rta);
    
    return $response;
    }
   
}