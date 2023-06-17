<?php

namespace App\Http\Controllers;

use App\Http\Requests\NumberCodeRequest;
use App\Http\Requests\SmsHistoryRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\NumberResource;
use App\Http\Resources\SmsHistoryResource;
use App\Models\VirtualPhone;
use App\Services\SmsHistoryService;
use App\Services\VirtualPhoneInitialScarap;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VirtualPhoneController extends Controller
{
    use ResponseJson;

    private VirtualPhoneInitialScarap $initialScrap;
    private SmsHistoryService $smsHistory;

    public function __construct(VirtualPhoneInitialScarap $initialScrap, SmsHistoryService $smsHistory)
    {
        $this->initialScrap = $initialScrap;
        $this->smsHistory = $smsHistory;
    }

    public function initialScrap()
    {
        $initialScrap = $this->initialScrap->initialScrap();

        return $this->data(
            $initialScrap["code"],
            $initialScrap["success"],
            $initialScrap["msg"],
            null
        );
    }

    public function getCountries()
    {
        $countries = VirtualPhone::all()->unique("countryCode");

        return $this->data(
            Response::HTTP_OK,
            true,
            "",
            CountryResource::collection($countries)
        );
    }

    public function getNumbers(NumberCodeRequest $request)
    {
        $getnumbersByCountryCode = VirtualPhone::where("countryCode", $request->countryCode)->get()->pluck("code_number");

        return $this->data(
            Response::HTTP_OK,
            true,
            "",
            $getnumbersByCountryCode
        );
    }

    public function smsHistory(SmsHistoryRequest $request)
    {
        $number = $request->countryCode . $request->phoneNumber;
        $smsHistory = $this->smsHistory->getSmsHistory($number);

        if($smsHistory){
            return $this->data(
                $smsHistory["code"],
                $smsHistory["success"],
                $smsHistory["msg"],
                SmsHistoryResource::collection($smsHistory["data"])
            );
        }

        return $this->data(
            Response::HTTP_NOT_FOUND,
            false,
            "Phone number does not exist on our Database.",
            null
        );

    }
}
