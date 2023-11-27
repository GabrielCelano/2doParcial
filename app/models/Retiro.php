<?php
require_once './db/AccesoDb.php';
require_once './exceptions/PropiedadInvalidaExcepcion.php';

class Retiro{
    public $id;
    public $idCuenta;
    public $tipoCta;
    public $fecha;
    public $importe;

    public function SetId($id){
        $id = intval($id);
        if(isset($id) && is_numeric($id))
            $this->id = $id;
        else
            throw new PropiedadInvalidaException("Error al asignar id.");
    }

    public function SetIdCuenta($idCuenta){
        $idCuenta = intval($idCuenta);
        if(isset($idCuenta) && is_numeric($idCuenta))
            $this->idCuenta = $idCuenta;
        else
            throw new PropiedadInvalidaException("Nro de Cuenta invalido.");
    }

    public function SetTipoCta($tipoCta){
        $tipoIngresado = strtolower($tipoCta);
        $tiposValidos = ['ca$', 'cau$s', 'cc$', 'ccu$s'];

        if (isset($tipoIngresado) && in_array($tipoIngresado, $tiposValidos)) {
            $this->tipoCta = $tipoIngresado;
        }else{
            throw new PropiedadInvalidaException("Tipo de cuenta invalido.");
        }
    }

    public function SetFecha($fecha){
        $this->fecha = $fecha;
    }
    
    public function SetImporte($importe){
        $importe = intval($importe);
        if(isset($importe) && is_numeric($importe))
            $this->importe = $importe;
        else
            throw new PropiedadInvalidaException("Importe invalido.");
    }

    public function Retiro(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO retiros (idCuenta, tipoCta, fecha, importe)
                                                        VALUES (:idCuenta, :tipoCta, :fecha, :importe);");
        $consulta->bindValue(':idCuenta', $this->idCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCta', $this->tipoCta, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_INT);
        $consulta->execute();
    }
    
    public static function ObtenerRetiroId($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idCuenta, tipoCta, fecha, importe
                                                        FROM retiros
                                                        WHERE id = :id");
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Retiro');
    }

    public static function ConsultaA($fecha, $tipoCta){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT r.TipoCta, SUM(r.Importe) AS TotalRetirado
                                                        FROM retiros r
                                                        INNER JOIN cuentas c ON r.IdCuenta = c.Id
                                                        WHERE r.Fecha LIKE :fecha AND r.TipoCta = :tipoCta
                                                        GROUP BY r.TipoCta");
        $consulta->bindParam(':tipoCta', $tipoCta, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', "%$fecha%", PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ConsultaB($nombre, $apellido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT r.Id, r.IdCuenta, r.TipoCta, r.Fecha, r.Importe
                                                        FROM retiros r
                                                        INNER JOIN cuentas c ON r.IdCuenta = c.Id
                                                        WHERE c.Nombre = :nombre AND c.Apellido = :apellido");
        $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ConsultaC($fechaInicio, $fechaFin){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT r.Id, r.IdCuenta, CONCAT(c.Nombre, ' ', c.Apellido) AS NombreCompleto, r.Fecha, r.Importe
                                                        FROM retiros r
                                                        INNER JOIN cuentas c ON r.IdCuenta = c.Id
                                                        WHERE DATE(r.Fecha) BETWEEN :fechaInicio AND :fechaFin
                                                        ORDER BY NombreCompleto");
        $consulta->bindParam(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        $consulta->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ConsultaD(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT r.Id, r.IdCuenta, r.TipoCta, r.Fecha, r.Importe
                                                        FROM retiros r
                                                        ORDER BY r.TipoCta");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}