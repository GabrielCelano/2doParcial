<?php
require_once './models/Deposito.php';
require_once './models/Cuenta.php';

class DepositoController extends Deposito{
    public function DepositoCuenta($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();

            $cuentas = Cuenta::ObtenerCuentaNroTipo($parametros['idCuenta'], $parametros['tipoCta']);
            
            if(!empty($cuentas)){
                $fotos = $request->getUploadedFiles();
                $deposito = new Deposito();
                $deposito->SetIdCuenta($parametros['idCuenta']);
                $deposito->SetTipoCta($parametros['tipoCta']);
                $deposito->SetImporte($parametros['importe']);
                $deposito->SetFecha(date("Y-m-d H:i:s"));

                $deposito->Deposito($fotos, $parametros['tipoCta']);
                Cuenta::SumarSaldo($deposito->idCuenta, $deposito->importe);
                $payload = json_encode(array("mensaje" => "El deposito se realizo con exito."));
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
            $result = Deposito::ConsultaA($parametros['fecha'], $parametros['tipoCta']);

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
            $result = Deposito::ConsultaB($parametros['nombre'], $parametros['apellido']);

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
            $result = Deposito::ConsultaC($parametros['fechaInicio'], $parametros['fechaFin']);

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
            $result = Deposito::ConsultaD();

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