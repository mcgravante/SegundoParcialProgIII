<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;
use \Firebase\JWT\JWT;
use App\Models\User;

class AsignoProfesorMiddleware
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
                if ($user[0]->tipo_id == "3")
                {
                            $resp = $handler->handle($request);
                            $existingContent = (string) $resp->getBody();
                            if  ($existingContent == '1')
                            {
                                $response->getBody()->write('Update' ." ok");
                            }
                            else {
                                $response->getBody()->write('Update' ." failed");

                            }
                }else
                {
                    $response->getBody()->write("Debe ser administrador para realizar este registro");
                }
    }else{
        $response->getBody()->write("Token incorrecto, ud no es un usuario registrado");
    }
        return $response;
    }
}
