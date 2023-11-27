<?php
require_once './models/Cuenta.php';

class CuentaController extends Cuenta{
    public function AltaCuenta($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();

            $cuenta = Cuenta::ObtenerCuentaDoc($parametros['nroDoc']);
            
            if(!empty($cuenta)){
                Cuenta::SumarSaldo($cuenta[0]->id, $parametros['saldo']);
                $payload = json_encode(array("mensaje" => "Se actualizo el saldo de la cuenta con exito."));
            }else{
                $fotos = $request->getUploadedFiles();
                $cuenta = new Cuenta();
                $cuenta->SetNombre($parametros['nombre']);
                $cuenta->SetApellido($parametros['apellido']);
                $cuenta->SetTipoDoc($parametros['tipoDoc']);
                $cuenta->SetNroDoc($parametros['nroDoc']);
                $cuenta->SetMail($parametros['mail']);
                $cuenta->SetSaldo($parametros['saldo']);
                $cuenta->SetTipoCta($parametros['tipoCta']);
                $cuenta->SetHabilitada(true);
                $cuenta->Alta($fotos);
                $payload = json_encode(array("mensaje" => "Cuenta creada con exito."));
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

    public function ModificarCuenta($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();
            $cuentas = Cuenta::ObtenerCuentaNroCta($parametros['idCuenta']);
            if(!empty($cuentas)){
                $fotos = $request->getUploadedFiles();
                $cuenta = new Cuenta();
                $cuenta->id = $cuentas[0]->id;
                $cuenta->SetNombre($parametros['nombre']);
                $cuenta->SetApellido($parametros['apellido']);
                $cuenta->SetTipoDoc($parametros['tipoDoc']);
                $cuenta->SetNroDoc($parametros['nroDoc']);
                $cuenta->SetMail($parametros['mail']);
                $cuenta->SetTipoCta($parametros['tipoCta']);
                $cuenta->Modificar($fotos);
                $payload = json_encode(array("mensaje" => "Se modifico la cuenta con exito"));
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

    public function BorrarCuenta($request, $response, $args)
    {
        try {
            $parametros = $request->getQueryParams();
            $parametros['idCuenta'];
            $parametros['tipoCta'];

            $cuentas = Cuenta::ObtenerCuentaNroTipo($parametros['idCuenta'], $parametros['tipoCta']);
            
            if(!empty($cuentas)){
                Cuenta::Borrar($cuentas[0]);
                $payload = json_encode(array("mensaje" => "Cuenta dada de baja con exito."));
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

    public function ConsultarCuenta($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();
            $cuentas = Cuenta::ObtenerCuentaNroTipo($parametros['nroCuenta'], $parametros['tipoCta']);
            
            if(!empty($cuentas)){
                $payload = json_encode(array("mensaje" => "Tipo de cuenta: {$cuentas[0]->tipoCta} | Saldo de la cuenta: {$cuentas[0]->saldo}"));
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

    public function ExportarCuentas($request, $response, $args)
    {
        try {
        if(Cuenta::Exportar()){
            $payload = json_encode(array("Mensaje" => 'Cuentas exportados exitosamente.'));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        }catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function ImportarCuentas($request, $response, $args)
    {
        try {
            $archivos = $request->getUploadedFiles();
            $ruta = null;
            foreach ($archivos as $archivo) {
                if ($archivo instanceof \Psr\Http\Message\UploadedFileInterface) {
                    $ruta = "./backups/" . $archivo->getClientFilename();
                    break;
                }
            }
            if(Cuenta::Importar($ruta)){
                $payload = json_encode(array("Mensaje" => 'Cuentas importados exitosamente.'));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        }catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}