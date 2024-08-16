<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Caja;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;

class InicialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer = Customer::create([
            'businame' => 'Consumidor final',
            'typeidenti' => 'ci',
            'valueidenti' => '0999999999',
            'address' => 'dirección',
            'address' => 'dirección',
            'email' => 'final@mail',
            'phone' => '999999',
            'notes' => 'consumidor final por defecto'
        ]);

        $caja =  Caja::create([
            'nombre' => 'Caja Uno',
            'status' => '1',  //caja abierta
            'user_id' => '1',
        ]);

        // $categoria =  Category::create([
        //     'name' => 'Comida rápida'
        // ]);
        // $categoria =  Category::create([
        //     'name' => 'Bebidas calientes'
        // ]);
        // $categoria =  Category::create([
        //     'name' => 'Bebidas frías'
        // ]);
        // $categoria =  Category::create([
        //     'name' => 'Cortes'
        // ]);
        // $categoria =  Category::create([
        //     'name' => 'Postres'
        // ]);
        // $categoria =  Category::create([
        //     'name' => 'Servicios'
        // ]);
        // $categoria =  Category::create([
        //     'name' => 'Gaseosas'
        // ]);


        // //productos
        // $product = Product::create([
        //     'category_id' => 3,
        //     'code' => 1,
        //     'name' => 'Agua',
        //     'cost' => 0.25,
        //     'price' => 0.75,
        //     'iva' => 0.00,
        //     'ice' => 0.00,
        //     'descuento' => 0.00,
        //     'price2' => 0.75,
        //     'stock' => 100,
        //     'minstock' => 10
        // ]);
        // $product = Product::create([
        //     'category_id' => 2,
        //     'code' => 2,
        //     'name' => 'Americano doble',
        //     'cost' => 1.00,
        //     'price' => 1.50,
        //     'iva' => 0.00,
        //     'ice' => 0.00,
        //     'descuento' => 0.00,
        //     'price2' => 1.50,
        //     'stock' => 100,
        //     'minstock' => 10
        // ]);

        Setting::create([
            'razonSocial' => 'CHOCHO ORELLANA ANGELA XIMENA',
            'nombreComercial' => 'CHOCHO ORELLANA ANGELA XIMENA',
            'ruc' => '0103844494001',
            'estab' => '001',
            'ptoEmi' => '001',
            'dirMatriz' => 'Dávila Chica y Manuel Moreno',
            'dirEstablecimiento' => 'Dávila Chica y Manuel Moreno',
            'telefono' => '0987308688',
            'email'=> 'khipusistemas@gmail.com',
            'ambiente' => '001',
            'tipoEmision' => '001',
            'contribuyenteEspecial' => 'revisar',
            'obligadoContabilidad' => 'NO',
            'logo' => 'noImage.jpg',
            'leyend' => 'Gracias por su compra',
            'printer' => 'epson',
        ]);
    }


}
