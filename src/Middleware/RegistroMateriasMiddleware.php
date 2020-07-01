<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;
use \Firebase\JWT\JWT;
use App\Models\User;

class RegistroMateriasMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        
        $requestBody = $request->getParsedBody();

        $headers = getallheaders();
        $token = $headers['token'];

        $decoded = JWT::decode($token, 'usuario', array('HS256'));
        $user = json_decode(User::whereRaw('email = ? AND clave = ?',array($decoded->email,$decoded->clave))->get());
        $response = new Response();

        if ($user != [])
        {
            if ((isset($requestBody['materia']) && $requestBody['materia']!="") 
            && (isset($requestBody['cuatrimestre']) && $requestBody['cuatrimestre']!="")
            && (isset($requestBody['vacantes']) && $requestBody['vacantes']!="")
            && (isset($requestBody['profesor']) && $requestBody['profesor']!=""))
            {
                if ($user[0]->tipo_id == "3")
                {
                    $profesor = json_decode(User::where('id', $requestBody['profesor'])->get());
                    if($profesor != []  && $profesor[0]->tipo_id == 2)
                    {
                            $resp = $handler->handle($request);
                            $existingContent = (string) $resp->getBody();
                            $response->getBody()->write('Registro de materia' . $existingContent);
                    } else 
                    {
                        $response->getBody()->write("No existe profesor con ese id");
                    }
                }else
                {
                    $response->getBody()->write("Debe ser administrador para realizar este registro");
                }
        } else{
            $response->getBody()->write("Faltan datos");
        }
    }else{
        $response->getBody()->write("Token incorrecto, ud no es un usuario registrado");
    }
        return $response;
    }
}
