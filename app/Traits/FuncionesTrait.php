<?php

namespace App\Traits;

use DOMDocument;

trait FuncionesTrait{


    private $URL_RECE_SRI_1 = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
    private $URL_RECE_SRI_2 = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
    private $URL_AUTO_SRI_1 = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
    private $URL_AUTO_SRI_2 = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';

    public function comprobantes($directorio)
    {
        // Array en el que obtendremos los resultados
        $res = array();
        // Agregamos la barra invertida al final en caso de que no exista
        if (substr($directorio, -1) != "/"){$directorio .= "/";}
        // Creamos un puntero al directorio y obtenemos el listado de archivos
        $dir = @dir($directorio) or die("getFileList: Error abriendo el directorio $directorio para leerlo");
        while (($archivo = $dir->read()) !== false) {
            // Obviamos los archivos ocultos
            if ($archivo[0] == "."){continue;}
            if (is_dir($directorio . $archivo)) {
                $res[] = $archivo;
            } else if (is_readable($directorio . $archivo)) {
                $res[] = $archivo;
            }
        }
        $dir->close();
        return $res;
    }

    function wsdl($ambiente){
        $wsdl_rece_aut = Array('recepcion'=>'',
                               'autorizacion'=>'');
        if($ambiente == 1){
            $wsdl_rece_aut['recepcion'] = $this->URL_RECE_SRI_1;
            $wsdl_rece_aut['autorizacion'] = $this->URL_AUTO_SRI_1;
        }else{
            $wsdl_rece_aut['recepcion'] = $this->URL_RECE_SRI_2;
            $wsdl_rece_aut['autorizacion'] = $this->URL_AUTO_SRI_2;
        }
        return $wsdl_rece_aut;
    }
    function crearXmlAutorizado($estado,$numeroAutorizacion,$fechaAutorizacion,$comprobanteAutorizacion,$ruta_autorizados,$nuevo_xml){
        $xml = new DOMDocument();
        $xml_autor = $xml->createElement('autorizacion');
        $xml_estad = $xml->createElement('estado', $estado);
        $xml_nauto = $xml->createElement('numeroAutorizacion', $numeroAutorizacion);
        $xml_fauto = $xml->createElement('fechaAutorizacion', $fechaAutorizacion);
        $xml_compr = $xml->createElement('comprobante');
        $xml_autor->appendChild($xml_estad);
        $xml_autor->appendChild($xml_nauto);
        $xml_autor->appendChild($xml_fauto);
        $xml_compr->appendChild($xml->createCDATASection($comprobanteAutorizacion));
        $xml_autor->appendChild($xml_compr);
        $xml->appendChild($xml_autor);
        $ms = $xml->save($ruta_autorizados . $nuevo_xml);
        chmod($ruta_autorizados.$nuevo_xml, 0755);
        return $ms;
    }

    function recepcion($xmlname){
        $srtxmlfirmado = file_get_contents($this->PATH_XML_FIRMADO.$xmlname);
        $data['xml'] = base64_encode($srtxmlfirmado);
        try{
            $client = new nusoap_client(wsdl('S'), true);
            //$client = new \nusoap_client('http://my-laravel-installation.com/api?wsdl', true);
            $client->soap_defencoding = 'utf-8';
            $client->xml_encoding = 'utf-8';
            $client->decode_utf8 = false;
            $response = $client->call('validarComprobante', $data);
        }catch(Exception $e){
            $response = "Error!<br>";
            $response .= $e->getMessage().'<br>';
            $response .= 'Last response: ' . $client->response . '<br>';
        }
        return $response;
    }

    function correos($correo){
        try{
            $filep = 'C:\\laragon\\www\\FACTURACION_ENVIO\\facturacionphp\\comprobantes\\pdf\\prueba.pdf';

            $filex = 'C:\\laragon\\www\\FACTURACION_ENVIO\\facturacionphp\\comprobantes\\autorizados\\prueba.xml';
            // Creamos una nueva instancia
            $mail = new PHPMailer();
            // Activamos el servicio SMTP
            $mail->isSMTP();
            // Activamos / Desactivamos el "debug" de SMTP (Lo activo para ver en el HTML el resultado)
            // 0 = Apagado, 1 = Mensaje de Cliente, 2 = Mensaje de Cliente y Servidor
            $mail->SMTPDebug = 0;
            // Log del debug SMTP en formato HTML
            $mail->Debugoutput = 'html';
            // Servidor SMTP (para este ejemplo utilizamos gmail)
            $mail->Host = 'smtp.gmail.com';
            // Puerto SMTP
            $mail->Port = 587;
            // Tipo de encriptacion SSL ya no se utiliza se recomienda TSL
            $mail->SMTPSecure = 'tls';
            // Si necesitamos autentificarnos
            $mail->SMTPAuth = true;
            // Usuario del correo desde el cual queremos enviar, para Gmail recordar usar el usuario completo (usuario@gmail.com)
            $mail->Username = 'tiburcio2409@gmail.com';
            // Contraseña
            $mail->Password = 'ratasura.10';
            //Añadimos la direccion de quien envia el corre, en este caso
            //YARR Blog, primero el correo, luego el nombre de quien lo envia.
            $mail->setFrom('tiburcio2409@gmail.com', 'COMPROBANTE SRI');
            $mail->addAddress($correo);
            $mail->AddAttachment($filep, 'prueba.pdf');
            $mail->AddAttachment($filex, 'prueba.xml');
            $mail->Subject = 'COMPROBANTE ELECTRONICO';;
            //Creamos el mensaje
            $message = 'Le informamos que ha sido generado y autorizado por el SRI un comprobante electronico, el mismo que se encuentra disponible para su descarga';
            //Agregamos el mensaje al correo
            $mail->msgHTML($message);
            // Enviamos el Mensaje
            if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
            //echo "correo enviado ";
             }
            }catch(Exception $e){return "Ocurrio un error ".$e;}
        }




}
