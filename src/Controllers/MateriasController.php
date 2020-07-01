<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Materia;
use \Firebase\JWT\JWT;
use App\Models\User;

class MateriasController 
{

    /*   public function getAll(Request $request, Response $response, $args)
    {
        $rta = json_encode(mascota::all());

        // $response->getBody()->write("Controller");
        $response->getBody()->write($rta);

        return $response;
    }*/

    public function add(Request $request, Response $response, $args)
    {
        $requestBody = $request->getParsedBody();

        $requestMateria = $requestBody['materia'];
        $requestCuatrimestre = $requestBody['cuatrimestre'];
        $requestVacantes = $requestBody['vacantes'];
        $requestProfesor = $requestBody['profesor'];

        $materia = new Materia;

        $response->withHeader('Content-type', 'application/json');

        $materia->materia = $requestMateria;
        $materia->cuatrimestre = $requestCuatrimestre;
        $materia->vacantes = $requestVacantes;
        $materia->profesor_id = $requestProfesor;

        $rta = json_encode(array("ok" => $materia->save()));
        $response->getBody()->write($rta);
        
        return $response;
    }

    public function getById(Request $request, Response $response, $args)
    {

        $headers = getallheaders();
        $token = $headers['token'];

        $decoded = JWT::decode($token, 'usuario', array('HS256'));

        $user = json_decode(User::whereRaw('email = ? AND clave = ?',array($decoded->email,$decoded->clave))->get());
        $response->withHeader('Content-type', 'application/json');

        if($user[0]->tipo_id == "1")
        {
            $materias = json_decode(Materia::where('id', $args['id'])->get());
            $rta = json_encode($materias);
        }
        else{
            $materias = json_decode(Materia::where('materias.id', $args['id'])
            ->join('inscriptos', 'inscriptos.materia_id', 'materias.id')
            ->join('users', 'inscriptos.alumno_id', 'users.id')
            ->select('materias.id', 'materias.materia', 'materias.cuatrimestre', 
            'materias.vacantes', 'materias.profesor_id', 'users.email', 
            'users.nombre', 'users.legajo')
            ->get());
            $rta = json_encode($materias);
        }
        $response->getBody()->write($rta);
        
        return $response;
    }
}