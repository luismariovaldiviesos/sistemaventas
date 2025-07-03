<?php

$folders =  [
    'storage/app/comprobantes/autorizados',
    'storage/app/comprobantes/pdfs',
    'storage/app/comprobantes/xmlaprobados',
    'storage/app/comprobantes/enviados',
    'storage/app/comprobantes/firmados',
    'storage/app/comprobantes/no_firmados',
    'storage/app/comprobantes/no_autorizados',
    'storage/app/comprobantes/devueltos',
    'storage/app/comprobantes/no_enviados',
];

foreach ($folders as $folder) {
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
        file_put_contents("$folder/.gitignore", "*\n!.gitignore");
        echo "Creado: $folder\n";
    }
}
