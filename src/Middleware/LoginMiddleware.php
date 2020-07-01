<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Models\User;

class LoginMiddleware
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

        $rta = new Response();
        if ((isset($requestBody['email']) && $requestBody['email']!="") 
        && (isset($requestBody['clave']) && $requestBody['clave']!="") 
        )
        {
            $user = json_decode(User::whereRaw('email = ? AND clave = ?',array($requestBody['email'],$requestBody['clave']))->get());
            
            if($user != [])
            {
                $response = $handler->handle($request);
                $existingContent = (string) $response->getBody();
                $array = array(
                    "status" =>"200",
                    "token" => $existingContent
                );
                $rta->getBody()->write(json_encode($array));
            }else
            {
                $array = array(
                    "status" =>"404",
                    "message" => "No hay coincidencia entre email y contraseña"
                );
                $rta->getBody()->write(json_encode($array));
            }
        }else
        {
            $array = array(
                "status" =>"403",
                "message" => "Faltan datos"
            );
            $rta->getBody()->write(json_encode($array));
        }

        return $rta->withHeader('Content-type', 'application/json');
    }
}
