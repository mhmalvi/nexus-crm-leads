<?php

namespace App\Services;

use App\Models\Company;


class CompanyService{
    public function getCompany($client_id){
        return Company::find($client_id);
    }
}
