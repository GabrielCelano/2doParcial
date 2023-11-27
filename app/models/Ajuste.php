<?php
require_once './db/AccesoDb.php';
require_once './exceptions/PropiedadInvalidaExcepcion.php';

class Ajuste{
    public $idOperacion;
    public $tipoOperacion;
    public $motivo;
    public $importe;

    public function SetIdOperacion($idOperacion){
        $id = intval($idOperacion);
        if(isset($id) && is_numeric($id))
            $this->idOperacion = $id;
        else
            throw new PropiedadInvalidaException("Error al asignar id de la operacion. \n");
    }

    public function SetTipoOperacion($tipo){
        $tipoIngresado = strtolower($tipo);
        $tiposValidos = ['retiro', 'deposito'];

        if (isset($tipoIngresado) && in_array($tipoIngresado, $tiposValidos))
            $this->tipoOperacion = $tipoIngresado;
        else
            throw new PropiedadInvalidaException("Tipo de operacion invalido.");
    }
    
    public function SetMotivo($motivo){
        $this->motivo = $motivo;
    }
    
    public function SetImporte($importe){
        $importe = intval($importe);
        if(isset($importe) && is_numeric($importe))
            $this->importe = $importe;
        else
            throw new PropiedadInvalidaException("Error al asignar importe. \n");
    }

    public function Ajuste(){
        switch($this->tipoOperacion){
            case "retiro":
                    $retiros = Retiro::ObtenerRetiroId($this->idOperacion);
                    if(!empty($retiros)){
                        Cuenta::RestarSaldo($retiros[0]->idCuenta, $this->importe);
                        $this->RegistrarAjuste();
                    }else{
                        throw new Exception("No existe la operacion.");
                    }
                break;
                case "deposito":
                    $depositos = Deposito::ObtenerDepositoId($this->idOperacion);
                    if(!empty($depositos)){
                        Cuenta::SumarSaldo($depositos[0]->idCuenta, $this->importe);
                        $this->RegistrarAjuste();
                    }else{
                        throw new Exception("No existe la operacion.");
                    }
                break;
        }
    }

    public function RegistrarAjuste(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ajustes (idOperacion, tipoOperacion, motivo, importe)
                                                        VALUES (:idOperacion, :tipoOperacion, :motivo, :importe);");
        $consulta->bindValue(':idOperacion', $this->idOperacion, PDO::PARAM_INT);
        $consulta->bindValue(':tipoOperacion', $this->tipoOperacion, PDO::PARAM_STR);
        $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_INT);
        $consulta->execute();
    }
}