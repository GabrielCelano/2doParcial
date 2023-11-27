<?php
require_once './models/Retiro.php';
require_once './models/Cuenta.php';

class RetiroController extends Retiro{
    public function RetiroCuenta($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();

            $cuentas = Cuenta::ObtenerCuentaNroTipo($parametros['idCuenta'], $parametros['tipoCta']);
            
            if(!empty($cuentas)){
                $retiro = new Retiro();
                $retiro->SetIdCuenta($parametros['idCuenta']);
                $retiro->SetTipoCta($parametros['tipoCta']);
                $retiro->SetImporte($parametros['importe']);
                $retiro->SetFecha(date("Y-m-d H:i:s"));
                Cuenta::RestarSaldo($retiro->idCuenta, $retiro->importe);
                $retiro->Retiro();
                $payload = json_encode(array("mensaje" => "El retiro se realizo con exito."));
            }else{
                $payload = json_encode(array("mensaje" => "La cuenta no existe."));
            }

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function Consulta1($request, $response, $args)
    {
        try {
            $parametros = $request->getQueryParams();
            $result = Retiro::ConsultaA($parametros['fecha'], $parametros['tipoCta']);

            if(!empty($result))
                $payload = json_encode(array("mensaje" => $result));
            else    
                $payload = json_encode(array("mensaje" => "No existen registros."));  
            
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function Consulta2($request, $response, $args)
    {
        try {
            $parametros = $request->getQueryParams();
            $result = Retiro::ConsultaB($parametros['nombre'], $parametros['apellido']);

            if(!empty($result))
                $payload = json_encode(array("mensaje" => $result));
            else    
                $payload = json_encode(array("mensaje" => "No existen registros."));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function Consulta3($request, $response, $args)
    {
        try {
            $parametros = $request->getQueryParams();
            $result = Retiro::ConsultaC($parametros['fechaInicio'], $parametros['fechaFin']);

            if(!empty($result))
                $payload = json_encode(array("mensaje" => $result));
            else    
                $payload = json_encode(array("mensaje" => "No existen registros."));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function Consulta4($request, $response, $args)
    {
        try {
            $result = Retiro::ConsultaD();

            if(!empty($result))
                $payload = json_encode(array("mensaje" => $result));
            else    
                $payload = json_encode(array("mensaje" => "No existen registros."));

            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}