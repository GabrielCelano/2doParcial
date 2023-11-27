<?php
require_once './db/AccesoDb.php';
require_once './helpers/ImagenHelper.php';
require_once './exceptions/PropiedadInvalidaExcepcion.php';

class Deposito{
    public $id;
    public $idCuenta;
    public $tipoCta;
    public $fecha;
    public $importe;
    public $foto;

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

    public function SetImagen($foto){
        if(isset($foto))
            $this->foto = $foto;
        else
            throw new PropiedadInvalidaException("Error al asignar importe.");
    }

    public function Deposito($fotos){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta("INSERT INTO depositos (idCuenta, tipocta, fecha, importe)
                                                                VALUES (:idCuenta, :tipoCta, :fecha, :importe);");
        $consultaInsert->bindValue(':idCuenta', $this->idCuenta, PDO::PARAM_STR);
        $consultaInsert->bindValue(':tipoCta', $this->tipoCta, PDO::PARAM_STR);
        $consultaInsert->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consultaInsert->bindValue(':importe', $this->importe, PDO::PARAM_INT);
        $consultaInsert->execute();

        $depositos = self::ObtenerDepositoFecha($this->idCuenta, $this->fecha);
        $ruta = ImagenHelper::MoverImagenDeposito($fotos, "./ImagenesDeDepositos/2023/", $this->idCuenta, $this->tipoCta, $depositos[0]->id);
        $consultaUpdate = $objAccesoDatos->prepararConsulta("UPDATE depositos
                                                                SET foto = :foto
                                                                WHERE id = :id");
        $consultaUpdate->bindValue(':id', $depositos[0]->id, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':foto', $ruta, PDO::PARAM_STR);
        $consultaUpdate->execute();   
    }

    public function ObtenerDepositoFecha($idCuenta, $fecha){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idCuenta, tipoCta, fecha, importe, foto
                                                        FROM depositos 
                                                        WHERE idCuenta = :idCuenta AND fecha = :fecha");
        $consulta->bindParam(':idCuenta', $idCuenta, PDO::PARAM_INT);
        $consulta->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
    }

    public static function ObtenerDepositoId($id){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idCuenta, tipoCta, fecha, importe, foto
                                                        FROM depositos 
                                                        WHERE id = :id");
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
    }

    public static function ConsultaA($fecha, $tipoCta){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT d.TipoCta, SUM(d.Importe) AS TotalDepositado
                                                        FROM depositos d
                                                        INNER JOIN cuentas c ON d.IdCuenta = c.Id
                                                        WHERE d.Fecha LIKE :fecha AND d.TipoCta = :tipoCta
                                                        GROUP BY d.TipoCta;");
        $consulta->bindParam(':tipoCta', $tipoCta, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', "%$fecha%", PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ConsultaB($nombre, $apellido){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT d.Id, d.IdCuenta, d.TipoCta, d.Fecha, d.Importe, d.Foto
                                                        FROM depositos d
                                                        INNER JOIN cuentas c ON d.IdCuenta = c.Id
                                                        WHERE c.Nombre = :nombre AND c.Apellido = :apellido");
        $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ConsultaC($fechaInicio, $fechaFin){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT d.Id, d.IdCuenta, CONCAT(c.Nombre, ' ', c.Apellido) AS NombreCompleto, d.Fecha, d.Importe, d.Foto
                                                        FROM depositos d
                                                        INNER JOIN cuentas c ON d.IdCuenta = c.Id
                                                        WHERE DATE(d.Fecha) BETWEEN :fechaInicio AND :fechaFin
                                                        ORDER BY NombreCompleto");
        $consulta->bindParam(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        $consulta->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ConsultaD(){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT d.Id, d.IdCuenta, d.TipoCta, d.Fecha, d.Importe, d.Foto
                                                        FROM depositos d
                                                        ORDER BY d.TipoCta");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}