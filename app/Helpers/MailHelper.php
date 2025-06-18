<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use App\Models\SmtpSetting;
use App\Models\Setting;


class MailHelper
{

    public static function setSmtpFromDatabase($empresa_id = null){

        $empresa  =  empresa();

        $smtp =  SmtpSetting::where('empresa_id', $empresa->id)->first();
        if($smtp){
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $smtp->host);
            Config::set('mail.mailers.smtp.port', $smtp->port);
            Config::set('mail.mailers.smtp.encryption', $smtp->encryption);
            Config::set('mail.mailers.smtp.username', $smtp->username);
            Config::set('mail.mailers.smtp.password', Crypt::decryptString($smtp->password));

            Config::set('mail.from.address', $smtp->from_address);
            Config::set('mail.from.name', $smtp->from_name);
            Config::set('mail.default', $smtp->mailer); // Generalmente 'smtp'
        }
       // dd ($smtp);
    }

}
