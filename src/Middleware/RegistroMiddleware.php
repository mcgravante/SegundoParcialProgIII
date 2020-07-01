<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;
use App\Models\User;
use Slim\Psr7\UploadedFile;


class RegistroMiddleware
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

        $response = new Response();

        if ((isset($requestBody['email']) && $requestBody['email']!="") 
        && (isset($requestBody['clave']) && $requestBody['clave']!="") 
        && (isset($requestBody['tipo'])&& $requestBody['tipo']!="")
        && (isset($requestBody['nombre']) && $requestBody['nombre']!="") 
        && (isset($requestBody['legajo']) && $requestBody['legajo']!=""))
        {
            if ( 1000<=$requestBody['legajo'] && $requestBody['legajo']<=2000 )
            {
                $userForEmail = json_decode(User::where('email', $requestBody['email'])->get());
                if ($userForEmail == [] )
                {
                    $userForLegajo = json_decode(User::where('legajo', $requestBody['legajo'])->get());
                    if ($userForLegajo == [] )
                    {
                        if ( $requestBody['tipo'] ==1 || $requestBody['tipo'] == 2 || $requestBody['tipo'] == 3)
                        {
                            $resp = $handler->handle($request);
                            $existingContent = (string) $resp->getBody();
                            $response->getBody()->write('Registro' . $existingContent);
                        } else {
                            $response->getBody()->write("No corresponde el tipo de usuario. 1 para alumnos, 2 para profesores, 3 para administradores");
                            $response->withStatus(400);
                        }
                    }else {
                        $response->getBody()->write("El legajo ya se encuentra registrado.");
                        $response->withStatus(400);
                    }
                }else
                {
                    $response->getBody()->write("El email ya se encuentra registrado.");
                    $response->withStatus(400);
                }
            } else {
                $response->getBody()->write("El legajo debe estar entre 1000 y 2000");
                $response->withStatus(400);
            }  
        } else 
        {
            $response->getBody()->write("No se pudo completar el registro, faltan datos");
            $response->withStatus(400);
        }
        return $response;

    }
}