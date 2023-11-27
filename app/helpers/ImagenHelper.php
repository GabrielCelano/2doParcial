<?php
class ImagenHelper{
    public static function MoverImagenCuenta($fotos, $ruta, $nroCuenta, $tipoCta){
        $directorioDestino = '.\\ImagenesDeCuentas\\2023';
        
        if (!file_exists($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                throw new Exception("No se pudo crear el directorio");
            }
        }
        
        $file = '';
        foreach ($fotos as $foto) {
            if ($foto instanceof \Psr\Http\Message\UploadedFileInterface) {
                $nuevoNombre = $nroCuenta . $tipoCta . '.' . pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION);
                $ruta = $ruta . $nuevoNombre;
                $file = $foto->getStream()->getMetadata('uri');
                break;
            }
        }
        
        move_uploaded_file($file , $ruta);
        return $ruta;
    }

    public static function MoverImagenDeposito($fotos, $ruta, $nroCuenta, $tipoCta, $idDeposito){
        $directorioDestino = '.\\ImagenesDeDepositos\\2023';

        if (!file_exists($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                    throw new Exception("No se pudo crear el directorio");
            }
        }

        $file = '';
        foreach ($fotos as $foto) {
            if ($foto instanceof \Psr\Http\Message\UploadedFileInterface) {
                $nuevoNombre = $nroCuenta . $tipoCta . $idDeposito . '.' . pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION);
                $ruta = $ruta . $nuevoNombre;
                $file = $foto->getStream()->getMetadata('uri');
                break;
            }
        }
        
        move_uploaded_file($file , $ruta);
        return $ruta;
    }

    public static function MoverImagenBackup($rutaOrigen, $rutaDestino){
        $nombreArchivo = basename($rutaOrigen);
        $directorioDestino = '.\\ImagenesBackupCuentas\\2023';

        if (!file_exists($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true)) {
                    throw new Exception("No se pudo crear el directorio");
            }
        }

        $rutaDestino = $directorioDestino . '\\' . $nombreArchivo;
        
        if (rename($rutaOrigen, $rutaDestino)) {
            return true;
        } else {
            return false;
        }
    }
}