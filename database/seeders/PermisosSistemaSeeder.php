<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {



        // dashboard
       Permission::create([
        'name' => 'ver_dash',
        'guard_name' => 'web',
        ]);

        //categorias
        Permission::create([
            'name' => 'ver_categoria',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_categoria',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_categoria',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_categoria',
            'guard_name' => 'web',
        ]);
        //productos
        Permission::create([
            'name' => 'ver_producto',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_producto',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_producto',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_producto',
            'guard_name' => 'web',
        ]);
        //facturacion
        Permission::create([
            'name' => 'ver_facturacion',
            'guard_name' => 'web',
        ]);

        //cajas
        Permission::create([
            'name' => 'ver_caja',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'abrir_caja',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_caja',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_caja',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_caja',
            'guard_name' => 'web',
        ]);

        //arqueoes
        Permission::create([
            'name' => 'ver_arqueo',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_arqueo',
            'guard_name' => 'web',
        ]);
        //reprocesar
        Permission::create([
            'name' => 'ver_reprocesar',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'reprocesar',  // boton de reprocesar en cada factura con rpeblema
            'guard_name' => 'web',
        ]);

        //facturas emitidas
        Permission::create([
            'name' => 'ver_facturas_emitidas',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'ver_detalle_factura_emitida',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'anular_factura_emitida',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'reenviar_pdf_factura_emitida',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'descargar_archivo_factura_emitida',
            'guard_name' => 'web',
        ]);

        //facturas anuladas
        Permission::create([
            'name' => 'ver_facturas_anuladas',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'procesar_factura_anulada', //cuadno ya se ha eliminado del sri
            'guard_name' => 'web',
        ]);

        //notas de credito
        Permission::create([
            'name' => 'ver_notas_credito',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'ver_detalle_nota_credito',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'descargar_nota_credito',
            'guard_name' => 'web',
        ]);
        //cleintes
        Permission::create([
            'name' => 'ver_cliente',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_cliente',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_cliente',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_cliente',
            'guard_name' => 'web',
        ]);
        //ventar diaria
        Permission::create([
            'name' => 'ver_venta_diaria',
            'guard_name' => 'web',
        ]);

        //empresa
        Permission::create([
            'name' => 'ver_empresa',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_empresa',
            'guard_name' => 'web',
        ]);

        //roles
        Permission::create([
            'name' => 'ver_roles',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_roles',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_roles',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_roles',
            'guard_name' => 'web',
        ]);
        //permisos
        Permission::create([
            'name' => 'ver_permisos',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_permisos',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_permisos',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_permisos',
            'guard_name' => 'web',
        ]);
        //asginar permsisos a roles
        Permission::create([
            'name' => 'asignar_permisos',
            'guard_name' => 'web',
        ]);
        //usuarios
        Permission::create([
            'name' => 'ver_usuarios',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_usuarios',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_usuarios',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_usuarios',
            'guard_name' => 'web',
        ]);
        //descuentos
        Permission::create([
            'name' => 'ver_descuentos',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_descuentos',
            'guard_name' => 'web',
        ]);

        //impuestos
        Permission::create([
            'name' => 'ver_impuestos',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'crear_impuestos',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'editar_impuestos',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'eliminar_impuestos',
            'guard_name' => 'web',
        ]);
        //firma electrónica
        Permission::create([
            'name' => 'ver_firma',
            'guard_name' => 'web',
        ]);

    }
}
