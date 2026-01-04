<?php

namespace App\Classes;

use App\Models\Company;
use App\Models\User;

class UserSession
{
    public static function session_update($update_login = false, $company_id = null)
    {
        $bd_user = User::emtGet(auth()->user()->id);
        $company_id = ($company_id) ? $company_id : $bd_user->company_id;
        $company = Company::emtGet($company_id);
        session(['company_id' => $company_id]);
        session(['company' => $company]);
        if ($update_login) {
            $bd_user->last_login = now();
            $bd_user->save();
        }
    }
}
