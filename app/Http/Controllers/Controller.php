<?php

namespace App\Http\Controllers;

use App\Http\Helpers\NumberToLetter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @OA\Info(title="My First API", version="0.1")
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function convertAmoutToLetter($amount){
        $lettre = new NumberToLetter();
        $amountLetter = "";
        if($amount == 0 ){
            $amountLetter = "Zero Dinar (s)";
        }else{
            if($this->isDecimal($amount)){
                list($int, $float) = explode('.',  $amount);
                $amountLetter =  $lettre->Conversion($int)." Dinar(s)";
                $centime = "";
                if($float>0){
                    $centime =  $lettre->Conversion($float)." Centimes";
                }
                $amountLetter = $amountLetter.' et '.$centime;
            }else{
                $amountLetter =  $lettre->Conversion($amount)."Dinar(s)";

            }
        }


        return $amountLetter;
    }

    function isDecimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

}
