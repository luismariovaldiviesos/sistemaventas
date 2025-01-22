<?php

namespace App\Http\Controllers;

use App\Mail\FacturaMail;
use App\Models\Factura;
use App\Models\Setting;
use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PdfController extends Controller
{

    public  function pdfDowloader (Factura $factura){

        //$empresa =  Setting::get();
        // Limpia cualquier salida previa
        //dd('hola pdf ctm');
        //dd($factura);
        $empresa =  Cache::get('settings');
        ob_end_clean();
        ob_start();
        // foreach($factura->detalles as $detalle){
        //     dd($detalle);
        // };
        //dd($empresa->razonSocial, $factura->detalles);
        // Crear el PDF con FPDF
        $pdf = new Fpdf();
        $pdf->SetCreator($empresa->razonSocial);
		$pdf->SetAuthor($empresa->razonSocial);
		$pdf->SetTitle('factura');
		$pdf->SetSubject('PDF');
		$pdf->SetKeywords('FPDF, PDF, cheque, impresion, guia');
		$pdf->SetMargins('10', '10', '10');
		$pdf->SetAutoPageBreak(TRUE);
		$pdf->SetFont('Arial', '', 7);
		$pdf->AddPage();
		//$pdf->Image('../img/logo.jpg',35,15,34);
		$pdf->SetXY(107, 10);
		$pdf->Cell(93, 84, '', 1, 1);
		$pdf->SetXY(10, 54);
		$pdf->Cell(93, 40, '', 1, 1);
		$pdf->SetXY(10, 98);
		$pdf->Cell(190, 12, '', 1, 1);
		$pdf->SetXY(10, 114);
		$pdf->Cell(190, 173, '', 0, 1);
		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 54);$pdf->Cell(93, 10, $empresa->razonSocial, 0 , 1, 'C');
		 $pdf->SetFont('Arial', '', 6);$pdf->SetXY(10, 59);$pdf->Cell(93, 10, ' MATRIZ', 0 , 1, 'L');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, 68);$pdf->MultiCell(93, 10, $empresa->dirMatriz, 0 , 'C');
		$pdf->SetFont('Arial', '', 6);$pdf->SetXY(25, 68);$pdf->MultiCell(78, 4, 'SUCURSAL', 0 , 'L');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, 80);$pdf->MultiCell(15, 4, $empresa->disSucursal, 0 , 'C');
		// $pdf->SetFont('Arial', '', 6);$pdf->SetXY(25, 80);$pdf->MultiCell(78, 4, 'VIA QUITO', 0 , 'L');
		$pdf->SetFont('Arial', 'B', 9);$pdf->SetXY(107, 10);$pdf->Cell(40, 8, 'RUC:'. ' '. $empresa->ruc, 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 18);$pdf->Cell(93, 8, 'FACTURA', 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 26);$pdf->Cell(40, 8, 'No: '. $factura->secuencial, 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 32);$pdf->Cell(40, 10, 'FECHA AUTORIZACION:' . '   '. $factura->fechaAutorizacion, 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(107, 42);$pdf->Cell(93, 8, 'NUMERO DE AUTORIZACION', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(107, 50);$pdf->Cell(93, 10, $factura->numeroAutorizacion, 0 , 1, 'C');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(107, 66);$pdf->Cell(93, 4, 'CLAVE DE ACCESO', 0 , 1, 'C');

            $barcodeGenerator = new BarcodeGeneratorPNG();

        // Generar el código de barras
        $barcodeData = $barcodeGenerator->getBarcode(
            (string) $factura->numeroAutorizacion,
            BarcodeGeneratorPNG::TYPE_CODE_128
        );
        // Guardar el código de barras como un archivo temporal
        $barcodeFile = 'barra_temp.png'; // Nombre del archivo temporal
        file_put_contents($barcodeFile, $barcodeData);
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(107, 80);
        // Insertar el código de barras exactamente en la posición deseada
        $pdf->Image($barcodeFile, 108, 70, 90, 10); // Coordenadas X, Y, ancho y alto del código de barras
		$pdf->Cell(93, 5, $factura->numeroAutorizacion, 0 , 1, 'C');

		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 98);$pdf->Cell(30, 3, 'RAZON SOCIAL', 0 , 1, 'C');
		$pdf->SetXY(10, 101);$pdf->Cell(30, 3, 'NOMBRES Y APELLIDOS', 0 , 0, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(40, 98);$pdf->MultiCell(160, 3, $factura->customer->businame,0,'L');
		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 104);$pdf->Cell(30, 6, 'FECHA DE EMISION', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(40, 104);$pdf->Cell(100, 6, $factura->fechaAutorizacion, 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(140, 104);$pdf->Cell(30, 6, 'IDENTIFICACION', 0 , 1);
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(170, 104);$pdf->Cell(30, 6, $factura->customer->valueidenti, 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);

		$pdf->SetXY(10, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(10, 114);$pdf->Cell(13, 3, 'Cod.', 0 , 1, 'C');
		$pdf->SetXY(10, 117);$pdf->Cell(13, 3, 'Principal', 0 , 1, 'C');
		$pdf->SetXY(23, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(23, 114);$pdf->Cell(13, 3, 'Cod.', 0 , 1, 'C');
		$pdf->SetXY(23, 117);$pdf->Cell(13, 3, 'Auxiliar', 0 , 1, 'C');
		$pdf->SetXY(36, 114);$pdf->Cell(13, 6, 'Cant', 1 , 1, 'C');
		$pdf->SetXY(49, 114);$pdf->Cell(110, 6, 'DESCRIPCION', 1 , 1, 'C');
		$pdf->SetXY(159, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(159, 114);$pdf->Cell(13, 3, 'Precio', 0 , 1, 'C');
		$pdf->SetXY(159, 117);$pdf->Cell(13, 3, 'Unitario', 0 , 1, 'C');
		$pdf->SetXY(172, 114);$pdf->Cell(15, 6, 'Descuento', 1 , 1, 'C');
		$pdf->SetXY(187, 114);$pdf->Cell(13, 6, false, 1 , 1);
		$pdf->SetXY(187, 114);$pdf->Cell(13, 3, 'Precio', 0 , 1, 'C');
		$pdf->SetXY(187, 117);$pdf->Cell(13, 3, 'Total', 0 , 1, 'C');
		//CABECERA KARDEX TOTALES

		$ejey = 120;
        foreach($factura->detalles as $detalle){
            //dd($detalle);
		$pdf->SetXY(10, $ejey);$pdf->Cell(13, 10, $detalle->product_id, 1 , 1, 'C');  // codigo producto
		$pdf->SetXY(23, $ejey);$pdf->Cell(13, 10, '', 1 , 1, 'C');
		$pdf->SetXY(36, $ejey);$pdf->Cell(13, 10, $detalle->cantidad, 1 , 1, 'C');$pdf->SetFont('Arial', 'B', 5);  //cantidad
		$pdf->SetXY(49, $ejey);$pdf->Cell(110, 10, '', 1 , 0);
		$pdf->SetXY(49, $ejey);$pdf->MultiCell(110, 5,$detalle->descripcion,'L');$pdf->SetFont('Arial', 'B', 7);  //pridcuto
		$pdf->SetXY(159, $ejey);$pdf->Cell(13, 10, $detalle->precioUnitario, 1 , 1, 'C');  //precio unitario
        $descuento = ($detalle->descuento * $detalle->precioUnitario / 100) * $detalle->cantidad;
		$pdf->SetXY(172, $ejey);$pdf->Cell(15, 10, number_format($descuento,2), 1 , 1, 'C');  //descueto
        // Total (precio total menos el descuento aplicado)
        $total = ($detalle->precioUnitario * $detalle->cantidad) - $descuento;
		$pdf->SetXY(187, $ejey);$pdf->Cell(13, 10, number_format($total,2), 1 , 1, 'C');  //total

		$ejey += 10;
		//$ejey += 4;
    }
        //KARDEX TOTALES
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(120, $ejey);$pdf->Cell(50, 4, 'SUBTOTAL', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+4);$pdf->Cell(50, 4, 'IVA 0%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+8);$pdf->Cell(50, 4, 'IVA 12%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+12);$pdf->Cell(50, 4, 'DESCUENTO $', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+16);$pdf->Cell(50, 4, 'VALOR TOTAL', 1 , 1, 'L');
		$pdf->SetXY(170, $ejey);$pdf->Cell(30, 4, $factura->total, 1 , 1, 'R');//SUBTOTAL
		$pdf->SetXY(170, $ejey+4);$pdf->Cell(30, 4, $factura->subtotal0, 1 , 1, 'R');//IVA 0
		$pdf->SetXY(170, $ejey+8);$pdf->Cell(30, 4, $factura->subtotal12, 1 , 1, 'R');//VALOR IVA
		$pdf->SetXY(170, $ejey+12);$pdf->Cell(30, 4, $factura->descuento, 1 , 1, 'R');//VALOR DESCUENTO
		$pdf->SetXY(170, $ejey+16);$pdf->Cell(30, 4, $factura->total, 1 , 1, 'R');//VALOR CON IVA
		//INFO ADICIONAL
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetXY(10, $ejey);$pdf->Cell(105, 6, 'INFORMACION ADICIONAL', 1 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);
		$pdf->SetXY(10, $ejey+6);$pdf->Cell(20, 6, 'Email empresa:', 'L' , 1, 'L');
		$pdf->SetXY(10, $ejey+12);$pdf->Cell(20, 6, 'Email cliente:', 'L' , 1, 'L');
		$pdf->SetXY(10, $ejey+18);$pdf->Cell(20, 6, 'Telefono cliente:', 'L' , 1, 'L');
		$pdf->SetXY(30, $ejey+6);$pdf->Cell(85, 6, $empresa->email, 'R' , 1, 'L'); //email empresa
		$pdf->SetXY(30, $ejey+12);$pdf->Cell(85, 6, $factura->customer->email, 'R' , 1, 'L');  // email cliente
		$pdf->SetXY(30, $ejey+18);$pdf->Cell(85, 6,  $factura->customer->phone, 'R' , 1, 'L');  //telefoo cliente
		$pdf->SetXY(10, $ejey+24);$pdf->MultiCell(105, 10,  $factura->customer->address, 'LRB', 'L'); //direccio  cliente
		//FORMA DE PAGO


		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, $ejey+39);$pdf->Cell(75, 6, 'Forma de pago', 1 , 1, 'C');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(85, $ejey+39);$pdf->Cell(30, 6, 'Valor', 1 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(10, $ejey+45);$pdf->Cell(75, 6, 'SIN UTILIZACION DEL SISTEMA FINANCIERON', 'LRB' , 1, 'L');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(85, $ejey+45);$pdf->Cell(30, 6, $factura->total, 'RB' , 1, 'L');

        // Salida del PDF
        $pdfContent = $pdf->Output('S');
        $fileName = $factura->customer->businame .'_'.$factura->secuencial .'.pdf';
        Storage::disk('comprobantes/pdfs')->put($fileName, $pdfContent);
        $this->enviarFacturea($factura);
        //$this->noty('PDF CREADO   CORRECTAMENTE !!!!!!');
        return response($pdf->Output('D',$factura->customer->businame.'.pdf'));

    }


    public function enviarFacturea(Factura $factura)  {

        //dd($factura);
         // Rutas de los archivos
         $pdf_name =  $factura->customer->businame.'_'.$factura->secuencial;
         $xml_name =  $factura->customer->valueidenti.'_'.$factura->secuencial;
        //dd($pdf_name,$xml_name);
         //$archivo_x_firmar =  base_path('storage/app/comprobantes/no_firmados/'.$nombre_fact_xml.'.xml');
        //$pdfPath = base_path("storage/app/comprobantes/pdfs/{$pdf_name}.pdf");
        $pdfPath = base_path('storage/app/comprobantes/pdfs/'.$pdf_name.'.pdf');
        $xmlPath = base_path('storage/app/comprobantes/autorizados/'.$xml_name.'.xml');
        //dd($pdfPath, $xmlPath);
        //$pdf_enviar =  file_get_contents($pdfPath);
        //dd($pdf_enviar);
		if (!file_exists($pdfPath) || !file_exists($xmlPath)) {
		   // return back()->with('error', 'Los archivos necesarios no existen.');
		   dd('no se encuentran los archivos');
		}
		else {
			try {
				Mail::to($factura->customer->email)->send(new FacturaMail($factura, $pdfPath, $xmlPath));
			} catch (\Exception $e) {
				// Handle the error
				dd('Error al enviar el correo: ' . $e->getMessage());
			}
		}

    }
}
