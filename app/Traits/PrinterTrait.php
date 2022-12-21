<?php

namespace App\Traits;

use App\Http\Livewire\Settings;
use App\Models\Branch;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Customer;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\Models\Config;
use App\Models\Setting;
use Mike42\Escpos\CapabilityProfile;

trait PrinterTrait {

    public function PrintTicket($orderId)
    {

        $settings = Setting::first();

        if(!$settings) return;  // si tenemos registros continuamos, si no se corta y no imprime



        //$nombreImpresora = 'file';
        $connector = new WindowsPrintConnector($settings->printer);
        $printer =  new Printer($connector);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $logo = EscposImage::load($settings->logo);
        $printer->bitImage($logo);
        $printer->setTextSize(3, 3);
        $printer->text("$settings->name\n");
        $printer->selectPrintMode();
        $printer->text("$settings->address\n");
        $printer->text("$settings->phone\n");
        // $printer->text("las pencas\n");
        // $printer->text("cuenca\n");
        $printer->feed(); // linea en blanco

        $printer->setEmphasis(true); //negrita
        $printer->text("Comprobante de pago\n\n");
        $printer->setEmphasis(false); //texto normal

        //headers
        $order =  Order::find($orderId);
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("FACTURA: #  $order->id  \n");
        $printer->text("FACTURA: ".  Carbon::parse($order->created_at)->format('d/m/Y h:m') ." \n");
        $printer->text("CLIENTE: " . $order->customer->businame . "  \n");

        //detalles
        $items = array();
        foreach($order->details as $detail)
        {
            array_push($items, new item($detail->product->name . ' x' . $detail->quantity, $detail->price));
        }

        $itemsQty = new item('Articulos', $order->item);
        $itemsQty = new item('Efectivo', $order->cash, true);
        $itemsQty = new item('Total', $order->total, true);
        $change = new item('Cambio', number_format(($order->cash - $order->total), 2), true);

        //items
        $printer->text("============================================\n");
        $printer->setEmphasis(true); //negrita
        $concepts = new item("DESCRIPCION","IMPORTE",false);
        $printer->text($concepts->getAsString());
        $printer->setEmphasis(false); //negrita
        $printer->text("============================================\n");
        foreach($items as $item){
            $printer->text($item->getAsString());
        }
        $printer->text("----------------------------------------------\n");
        $printer->text("\n");

        $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer->text($itemsQty->getAsString());
        $printer->feed();
        //$printer->text($cash->getAsString());
        $printer->setEmphasis(true); //negrita
        //$printer->text($total->getAsString());
        $printer->setEmphasis(false); //negrita
        $printer->feed();
        $printer->text($change->getAsString());
        $printer->selectPrintMode();
        $printer->feed();

        //pie de ticket
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("$settings->leyend\n");
        $printer->text("khipu.com\n");
        $printer->feed(2);

        //codigo de barras

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $folio = str_pad($order->id, 7, "0", STR_PAD_LEFT); //0000010  PARA RELLENAR EL NUMERO DE FACTURA
        $printer->setBarcodeHeight(60); // altura del barcode
        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
        $printer->barcode($folio, Printer::BARCODE_CODE39);
        $printer->feed(2);

        // corrta el papel
        $printer->cut();
        $printer->close();

    }


}

class item{
    // propiedades
    private $name ;
    private $price;
    private $dollarSign;

     public function __construct($name = '', $price ='', $dollarSign = false)
     {
        $this->name =  $name;
        $this->price = $price;
        $this->dollarSign = $dollarSign;
     }

     public function getAsString()
     {
        $rightCols = 10;
        $leftCols = 36;

        if ($this->dollarSign) {
            $leftCols = ($leftCols / 2) - $rightCols / 2;
        }

        $left = str_pad($this->name, $leftCols);

        $sign =  ($this->dollarSign ? '$ ' : '');

        $right = str_pad($sign. $this->price, $rightCols, ' ', STR_PAD_LEFT);

        return "$left$right\n";
     }

     public function __toString()
     {
        return $this->getAsString();
     }
}
