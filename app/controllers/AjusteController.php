<?php
require_once './models/Ajuste.php';

class AjusteController extends Ajuste{
    public function AjustarDeposito($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();
            $parametros['idOperacion'];
            $parametros['motivo'];
            $parametros['importe'];

            $ajuste = new Ajuste();
            $ajuste->SetIdOperacion($parametros['idOperacion']);
            $ajuste->SetTipoOperacion('deposito');
            $ajuste->SetMotivo($parametros['motivo']);
            $ajuste->SetImporte($parametros['importe']);

            $ajuste->Ajuste();

            $payload = json_encode(array("mensaje" => "El ajuste se realizo con exito."));
            
            
            $response->getBody()->write($payload);
            return $response
            ->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $payload = json_encode(array("error" => $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    public function AjustarRetiro($request, $response, $args)
    {
        try {
            $parametros = $request->getParsedBody();
            $parametros['idOperacion'];
            $parametros['motivo'];
            $parametros['importe'];

            $ajuste = new Ajuste();
            $ajuste->SetIdOperacion($parametros['idOperacion']);
            $ajuste->SetTipoOperacion('retiro');
            $ajuste->SetMotivo($parametros['motivo']);
            $ajuste->SetImporte($parametros['importe']);

            $ajuste->Ajuste();

            $payload = json_encode(array("mensaje" => "El ajuste se realizo con exito."));
            
            
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