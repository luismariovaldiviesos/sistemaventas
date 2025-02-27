<?php
use App\Models\Setting;


if (!function_exists('empresa')) {
    function empresa($empresa_id = null) {
        // Si hay multi-tenant, filtra por empresa_id, de lo contrario, trae la primera
        return $empresa_id ? Setting::where('empresa_id', $empresa_id)->first() : Setting::first();
    }
}
