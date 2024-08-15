<?php

namespace App\Traits;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;

trait PdfTrait{


    function pdfFactura($correo) {

        $pdf =  new Fpdf();
        $pdf->SetCreator('ESTEBAN BAHAMONDE');
		$pdf->SetAuthor('ESTEBAN BAHAMONDE');
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
		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 54);$pdf->Cell(93, 10, 'MI EMPRESA COMERCIAL S.A', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 6);$pdf->SetXY(10, 59);$pdf->Cell(93, 10, ' QUITO-ECUADOR', 0 , 1, 'L');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, 68);$pdf->MultiCell(15, 4, 'Direccion Matriz', 0 , 'C');
		$pdf->SetFont('Arial', '', 6);$pdf->SetXY(25, 68);$pdf->MultiCell(78, 4, 'VIA QUITO', 0 , 'L');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, 80);$pdf->MultiCell(15, 4, 'Direccion Sucursal', 0 , 'C');
		$pdf->SetFont('Arial', '', 6);$pdf->SetXY(25, 80);$pdf->MultiCell(78, 4, 'VIA QUITO', 0 , 'L');
		$pdf->SetFont('Arial', 'B', 9);$pdf->SetXY(107, 10);$pdf->Cell(40, 8, 'RUC: 1791345444001', 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 18);$pdf->Cell(93, 8, 'FACTURA', 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 26);$pdf->Cell(40, 8, 'No: 001-001-000397201', 0 , 1);
		$pdf->SetFont('Arial', '', 9);$pdf->SetXY(107, 32);$pdf->Cell(40, 10, 'FECHA AUTORIZACION: 2020-09-20', 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(107, 42);$pdf->Cell(93, 8, 'NUMERO DE AUTORIZACION', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(107, 50);$pdf->Cell(93, 10, '2009202001179134544400110010010003971781234567815', 0 , 1, 'C');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(107, 66);$pdf->Cell(93, 4, 'CLAVE DE ACCESO', 0 , 1, 'C');
		//new barCodeGenrator('2009202001179134544400110010010003971781234567815', 1, 'barra.gif', 455, 60, false);
		//$pdf->Image('barra.gif', 108, 70, 90, 10);
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(107, 80);
		$pdf->Cell(93, 5, '2009202001179134544400110010010003971781234567815', 0 , 1, 'C');

		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 98);$pdf->Cell(30, 3, 'RAZON SOCIAL', 0 , 1, 'C');
		$pdf->SetXY(10, 101);$pdf->Cell(30, 3, 'NOMBRES Y APELLIDOS', 0 , 0, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(40, 98);$pdf->MultiCell(160, 3, 'ESTEBAN BAHAMONDE',0,'L');
		$pdf->SetFont('Arial', 'B', 6);$pdf->SetXY(10, 104);$pdf->Cell(30, 6, 'FECHA DE EMISION', 0 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(40, 104);$pdf->Cell(100, 6, '2020-09-20', 0 , 1);
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(140, 104);$pdf->Cell(30, 6, 'IDENTIFICACION', 0 , 1);
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(170, 104);$pdf->Cell(30, 6, '9999999999', 0 , 1);
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
		$pdf->SetXY(10, $ejey);$pdf->Cell(13, 10, 'ASDFQ', 1 , 1, 'C');
		$pdf->SetXY(23, $ejey);$pdf->Cell(13, 10, '', 1 , 1, 'C');
		$pdf->SetXY(36, $ejey);$pdf->Cell(13, 10, '1.00', 1 , 1, 'C');$pdf->SetFont('Arial', 'B', 5);
		$pdf->SetXY(49, $ejey);$pdf->Cell(110, 10, '', 1 , 0);
		$pdf->SetXY(49, $ejey);$pdf->MultiCell(110, 5,'MESA','L');$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(159, $ejey);$pdf->Cell(13, 10, '10.00', 1 , 1, 'C');
		$pdf->SetXY(172, $ejey);$pdf->Cell(15, 10, '0.00', 1 , 1, 'C');
		$pdf->SetXY(187, $ejey);$pdf->Cell(13, 10, '10.00', 1 , 1, 'C');
		$ejey += 10;
		$ejey += 4;
		//KARDEX TOTALES
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetXY(120, $ejey);$pdf->Cell(50, 4, 'SUBTOTAL', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+4);$pdf->Cell(50, 4, 'IVA 0%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+8);$pdf->Cell(50, 4, 'IVA 12%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+12);$pdf->Cell(50, 4, 'DESCUENTO 0.00%', 1 , 1, 'L');
		$pdf->SetXY(120, $ejey+16);$pdf->Cell(50, 4, 'VALOR TOTAL', 1 , 1, 'L');
		$pdf->SetXY(170, $ejey);$pdf->Cell(30, 4, '10.00', 1 , 1, 'R');//SUBTOTAL
		$pdf->SetXY(170, $ejey+4);$pdf->Cell(30, 4, '10.00', 1 , 1, 'R');//IVA 0
		$pdf->SetXY(170, $ejey+8);$pdf->Cell(30, 4, '0.00', 1 , 1, 'R');//VALOR IVA
		$pdf->SetXY(170, $ejey+12);$pdf->Cell(30, 4, '0.00', 1 , 1, 'R');//VALOR DESCUENTO
		$pdf->SetXY(170, $ejey+16);$pdf->Cell(30, 4, '0.00', 1 , 1, 'R');//VALOR CON IVA
		//INFO ADICIONAL
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetXY(10, $ejey);$pdf->Cell(105, 6, 'INFORMACION ADICIONAL', 1 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);
		$pdf->SetXY(10, $ejey+6);$pdf->Cell(20, 6, 'Email empresa:', 'L' , 1, 'L');
		$pdf->SetXY(10, $ejey+12);$pdf->Cell(20, 6, 'Email cliente:', 'L' , 1, 'L');
		$pdf->SetXY(10, $ejey+18);$pdf->Cell(20, 6, 'Telefono cliente:', 'L' , 1, 'L');
		$pdf->SetXY(30, $ejey+6);$pdf->Cell(85, 6, 'emailempresa@gmail.com', 'R' , 1, 'L');
		$pdf->SetXY(30, $ejey+12);$pdf->Cell(85, 6, 'ebahamondet@gmail.com', 'R' , 1, 'L');
		$pdf->SetXY(30, $ejey+18);$pdf->Cell(85, 6, '2421558', 'R' , 1, 'L');
		$pdf->SetXY(10, $ejey+24);$pdf->MultiCell(105, 10, 'Direccion cliente: av 10 de agosto', 'LRB', 'L');
		//FORMA DE PAGO


		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(10, $ejey+39);$pdf->Cell(75, 6, 'Forma de pago', 1 , 1, 'C');
		$pdf->SetFont('Arial', 'B', 7);$pdf->SetXY(85, $ejey+39);$pdf->Cell(30, 6, 'Valor', 1 , 1, 'C');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(10, $ejey+45);$pdf->Cell(75, 6, 'SIN UTILIZACION DEL SISTEMA FINANCIERON', 'LRB' , 1, 'L');
		$pdf->SetFont('Arial', '', 7);$pdf->SetXY(85, $ejey+45);$pdf->Cell(30, 6, '152.00', 'RB' , 1, 'L');
		    //SAVE
		//$pdf->Output('../comprobantes/pdf/prueba.pdf','F');
        $pdfContent = $pdf->Output('','S');
        //dd($pdf);
        //$pdfName = 'aleatorio'.'pdf';
        Storage::disk('comprobantes/pdfs')->put('PRUEBA.pdf',$pdfContent);
        //dd('pdf generado ');

    }

}
