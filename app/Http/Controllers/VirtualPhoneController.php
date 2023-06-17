<?php

namespace App\Http\Controllers;

use App\Services\VirtualPhoneInitialScarap;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;

class VirtualPhoneController extends Controller
{
    use ResponseJson;

    private VirtualPhoneInitialScarap $initialScrap;

    public function __construct(VirtualPhoneInitialScarap $initialScrap)
    {
        $this->initialScrap = $initialScrap;
    }

    public function initialScrap()
    {
        $initialScrap = $this->initialScrap->initialScrap();
        //dd($initialScrap);
    }
}
