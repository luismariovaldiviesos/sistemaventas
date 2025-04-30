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
       Permission::firstOrCreate([
        'name' => 'ver_dash',
        'guard_name' => 'web',
        ]);

        //categorias
        Permission::firstOrCreate([
            'name' => 'ver_categoria',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_categoria',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_categoria',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_categoria',
            'guard_name' => 'web',
        ]);
        //productos
        Permission::firstOrCreate([
            'name' => 'ver_producto',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_producto',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_producto',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_producto',
            'guard_name' => 'web',
        ]);
        //facturacion
        Permission::firstOrCreate([
            'name' => 'ver_facturacion',
            'guard_name' => 'web',
        ]);

        //cajas
        Permission::firstOrCreate([
            'name' => 'ver_caja',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'cerrar_caja',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'abrir_caja',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_caja',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_caja',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_caja',
            'guard_name' => 'web',
        ]);

        //arqueoes
        Permission::firstOrCreate([
            'name' => 'ver_arqueo',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_arqueo',
            'guard_name' => 'web',
        ]);
        //reprocesar
        Permission::firstOrCreate([
            'name' => 'ver_reprocesar',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'reprocesar',  // boton de reprocesar en cada factura con rpeblema
            'guard_name' => 'web',
        ]);

        //facturas emitidas
        Permission::firstOrCreate([
            'name' => 'ver_facturas_emitidas',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'ver_detalle_factura_emitida',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'anular_factura_emitida',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'reenviar_pdf_factura_emitida',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'descargar_archivo_factura_emitida',
            'guard_name' => 'web',
        ]);

        //facturas anuladas
        Permission::firstOrCreate([
            'name' => 'ver_facturas_anuladas',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'procesar_factura_anulada', //cuadno ya se ha eliminado del sri
            'guard_name' => 'web',
        ]);

        //notas de credito
        Permission::firstOrCreate([
            'name' => 'ver_notas_credito',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'ver_detalle_nota_credito',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'descargar_nota_credito',
            'guard_name' => 'web',
        ]);
        //cleintes
        Permission::firstOrCreate([
            'name' => 'ver_cliente',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_cliente',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_cliente',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_cliente',
            'guard_name' => 'web',
        ]);
        //ventar diaria
        Permission::firstOrCreate([
            'name' => 'ver_venta_diaria',
            'guard_name' => 'web',
        ]);

        //empresa
        Permission::firstOrCreate([
            'name' => 'ver_empresa',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_empresa',
            'guard_name' => 'web',
        ]);

        //roles
        Permission::firstOrCreate([
            'name' => 'ver_roles',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_roles',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_roles',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_roles',
            'guard_name' => 'web',
        ]);
        //permisos
        Permission::firstOrCreate([
            'name' => 'ver_permisos',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_permisos',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_permisos',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_permisos',
            'guard_name' => 'web',
        ]);
        //asginar permsisos a roles
        Permission::firstOrCreate([
            'name' => 'asignar_permisos',
            'guard_name' => 'web',
        ]);
        //usuarios
        Permission::firstOrCreate([
            'name' => 'ver_usuarios',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_usuarios',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_usuarios',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_usuarios',
            'guard_name' => 'web',
        ]);
        //descuentos
        Permission::firstOrCreate([
            'name' => 'ver_descuentos',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_descuentos',
            'guard_name' => 'web',
        ]);

        //impuestos
        Permission::firstOrCreate([
            'name' => 'ver_impuestos',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'crear_impuestos',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'editar_impuestos',
            'guard_name' => 'web',
        ]);
        Permission::firstOrCreate([
            'name' => 'eliminar_impuestos',
            'guard_name' => 'web',
        ]);
        //firma electrónica
        Permission::firstOrCreate([
            'name' => 'ver_firma',
            'guard_name' => 'web',
        ]);

        $this->agregaPermisosAdmin();

    }



    public  function agregaPermisosAdmin(){
        $permisos = Permission::all();
        $rol = \Spatie\Permission\Models\Role::findByName('Admin');
        foreach ($permisos as $permiso) {
            $rol->givePermissionTo($permiso->name);
        }
    }
}
