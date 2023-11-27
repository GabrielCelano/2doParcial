<?php
require_once './db/AccesoDb.php';
require_once './helpers/ImagenHelper.php';
require_once './exceptions/PropiedadInvalidaExcepcion.php';

class Cuenta{
    public $id;
    public $nombre;
    public $apellido;
    public $tipoDoc;
    public $nroDoc;
    public $mail;
    public $tipoCta;
    public $saldo;
    public $foto;
    public $habilitada;

    public function SetNombre($nombre){
        if (isset($nombre) && preg_match("/^[a-zA-Z]+$/", $nombre)) {
            $this->nombre = $nombre;
        }else{
            throw new PropiedadInvalidaException("Nombre invalido.");
        }
    }

    public function SetApellido($apellido){
        if (isset($apellido) && preg_match("/^[a-zA-Z]+$/", $apellido)) {
            $this->apellido = $apellido;
        }else{
            throw new PropiedadInvalidaException("Apellido invalido.");
        }
    }

    public function SetTipoDoc($tipoDoc){
        $tipoIngresado = strtolower($tipoDoc);
        $tiposValidos = ["dni", "ci", "pasaporte"];

        if (isset($tipoIngresado) && in_array($tipoIngresado, $tiposValidos)) {
            $this->tipoDoc = $tipoIngresado;
        }else{
            throw new PropiedadInvalidaException("Tipo de documento invalido.");
        }
    }

    public function SetNroDoc($nroDoc){
        $nroDoc = intval($nroDoc);
        if (isset($nroDoc) && preg_match('/^[0-9]+$/', $nroDoc)) {
            $this->nroDoc = $nroDoc;
        }else{
            throw new PropiedadInvalidaException("Nro de documento invalido.");
        }
    }

    public function SetMail($mail){
        if (isset($mail) && filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $this->mail = $mail;
        } else {
            throw new PropiedadInvalidaException("Mail invalido.");
        }
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

    public function SetSaldo($saldo = 0){
        $saldo = intval($saldo);
        if(isset($saldo) &&  is_numeric($saldo)){
            $this->saldo = $saldo;
        }else{
            throw new PropiedadInvalidaException("Saldo invalido.");
        }
    }

    public function SetFoto($foto){
        if(isset($foto))
            $this->foto = $foto;
    }

    public function SetHabilitada($habilitada){
        if(isset($habilitada))
            $this->habilitada = $habilitada;
    }

    public function Alta($fotos){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaInsert = $objAccesoDatos->prepararConsulta("INSERT INTO cuentas (nombre, apellido, tipoDoc, nroDoc, mail, tipoCta, saldo, habilitada)
                                                                VALUES (:nombre, :apellido, :tipoDoc, :nroDoc, :mail, :tipoCta, :saldo, :habilitada);");
        $consultaInsert->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consultaInsert->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consultaInsert->bindValue(':tipoDoc', $this->tipoDoc, PDO::PARAM_STR);
        $consultaInsert->bindValue(':nroDoc', $this->nroDoc, PDO::PARAM_INT);
        $consultaInsert->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consultaInsert->bindValue(':tipoCta', $this->tipoCta, PDO::PARAM_STR);
        $consultaInsert->bindValue(':saldo', $this->saldo, PDO::PARAM_INT);
        $consultaInsert->bindValue(':habilitada', $this->habilitada, PDO::PARAM_BOOL);
        $consultaInsert->execute();

        $cuenta = self::ObtenerCuentaDoc($this->nroDoc);                
        $ruta = "./ImagenesDeCuentas/2023/";
        $ruta = ImagenHelper::MoverImagenCuenta($fotos, $ruta, $cuenta[0]->id, $cuenta[0]->tipoCta);
        $consultaUpdate = $objAccesoDatos->prepararConsulta("UPDATE cuentas
                                                                SET foto = :foto
                                                                WHERE id = :id");
        $consultaUpdate->bindValue(':id', $cuenta[0]->id, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':foto', $ruta, PDO::PARAM_INT);
        $consultaUpdate->execute();                                                        
    }

    public function Modificar($fotos){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE cuentas
                                                        SET nombre = :nombre, apellido = :apellido, tipoDoc = :tipoDoc, nroDoc = :nroDoc, mail = :mail, tipoCta = :tipoCta
                                                        WHERE id = :id");
        $consulta->bindParam(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindParam(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindParam(':tipoDoc', $this->tipoDoc, PDO::PARAM_STR);
        $consulta->bindParam(':nroDoc', $this->nroDoc, PDO::PARAM_INT);
        $consulta->bindParam(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindParam(':tipoCta', $this->tipoCta, PDO::PARAM_STR);
        $consulta->execute();

        $ruta = "./ImagenesDeCuentas/2023/";
        $ruta = ImagenHelper::MoverImagenCuenta($fotos, $ruta, $this->id, $this->tipoCta);
        $consultaUpdate = $objAccesoDatos->prepararConsulta("UPDATE cuentas
                                                                SET foto = :foto
                                                                WHERE id = :id");
        $consultaUpdate->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consultaUpdate->bindValue(':foto', $ruta, PDO::PARAM_INT);
        $consultaUpdate->execute();    
    }

    public static function Borrar($cuenta){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE cuentas
                                                        SET habilitada = :habilitada
                                                        WHERE id = :id");
        $habilitada = false;
        $consulta->bindParam(':id', $cuenta->id, PDO::PARAM_INT);
        $consulta->bindParam(':habilitada', $habilitada, PDO::PARAM_BOOL);
        $consulta->execute();
        
        $ruta = "./ImagenesBackupCuentas/2023/" . basename($cuenta->foto);
        if(ImagenHelper::MoverImagenBackup($cuenta->foto, $ruta)){
            $consultaUpdate = $objAccesoDatos->prepararConsulta("UPDATE cuentas
                                                                    SET foto = :foto
                                                                    WHERE id = :id");
            $consultaUpdate->bindValue(':id', $cuenta->id, PDO::PARAM_INT);
            $consultaUpdate->bindValue(':foto', $ruta, PDO::PARAM_INT);
            $consultaUpdate->execute();
        }
    }

    public static function ObtenerCuentaDoc($nroDoc){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, tipoDoc, nroDoc, mail, tipoCta, saldo, foto, habilitada
                                                        FROM cuentas 
                                                        WHERE nroDoc = :nroDoc");
        $consulta->bindParam(':nroDoc', $nroDoc, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cuenta');
    }

    public static function ObtenerCuentaNroTipo($nroCuenta, $tipoCta){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, tipoDoc, nroDoc, mail, tipoCta, saldo, foto, habilitada
                                                        FROM cuentas 
                                                        WHERE id = :id AND tipoCta = :tipoCta");
        $consulta->bindParam(':id', $nroCuenta, PDO::PARAM_INT);
        $consulta->bindParam(':tipoCta', $tipoCta, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cuenta');
    }

    public static function ObtenerCuentaNroCta($nroCuenta){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, tipoDoc, nroDoc, mail, tipoCta, saldo, foto, habilitada
                                                        FROM cuentas 
                                                        WHERE id = :id");
        $consulta->bindParam(':id', $nroCuenta, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cuenta');
    }

    public static function SumarSaldo($nroCuenta, $saldo) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE cuentas
                                                        SET saldo = saldo + :saldo
                                                        WHERE id = :id");
        $consulta->bindParam(':id', $nroCuenta, PDO::PARAM_INT);
        $consulta->bindParam(':saldo', $saldo, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function RestarSaldo($nroCuenta, $saldo) {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaSaldo = $objAccesoDatos->prepararConsulta("SELECT saldo FROM cuentas WHERE id = :id");
        $consultaSaldo->bindParam(':id', $nroCuenta, PDO::PARAM_INT);
        $consultaSaldo->execute();
        $saldoActual = $consultaSaldo->fetchColumn();

        $nuevoSaldo = $saldoActual - $saldo;
        if ($nuevoSaldo >= 0) {
            $consultaActualizar = $objAccesoDatos->prepararConsulta("UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :id");
            $consultaActualizar->bindParam(':id', $nroCuenta, PDO::PARAM_INT);
            $consultaActualizar->bindParam(':nuevoSaldo', $nuevoSaldo, PDO::PARAM_INT);
            $consultaActualizar->execute();
        } else {
            throw new Exception("Saldo insuficiente para la operación");
        }
    }

    public static function Exportar()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM cuentas");
        $consulta->execute();

        $timestampBefore = time();
        $csv_filename = './backups/cuentas_' . $timestampBefore . '.csv';
        if (file_exists($csv_filename)) {
            $timestampAfter = time(); // Obtener la marca de tiempo actual
            $csv_filename = './backups/cuentas_' . $timestampAfter . '.csv';
        }

        $csv_file = fopen($csv_filename, 'w');

        while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($csv_file, $row);
        }
    
        fclose($csv_file);
        return true;
    }

    public static function Importar($csv_filename)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
        if (!file_exists($csv_filename)) {
            return false;
        }
        
        $csv_file = fopen($csv_filename, 'r');
    
        while (($row = fgetcsv($csv_file)) !== false) {
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO cuentas (nombre, apellido, tipoDoc, nroDoc, mail, tipoCta, saldo, foto, habilitada) 
                                                            VALUES (:1, :2, :3, :4, :5, :6, :7, :8, :9)");
            
            $consulta->bindParam(1, $row[1]); // Nombre
            $consulta->bindParam(2, $row[2]); // Apellido
            $consulta->bindParam(3, $row[3]); // TipoDoc
            $consulta->bindParam(4, $row[4]); // NroDoc
            $consulta->bindParam(5, $row[5]); // Mail
            $consulta->bindParam(6, $row[6]); // TipoCta
            $consulta->bindParam(7, $row[7]); // Saldo
            $consulta->bindParam(8, $row[8]); // Foto
            $consulta->bindParam(9, $row[9]); // Habilitada
            
            $consulta->execute();
        }
    
        fclose($csv_file);
        return true;
    }
}