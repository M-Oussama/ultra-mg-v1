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

    public function months(int $start){
        $months =[];
        for ($month = $start; $month <= 12; $month++) {
            $monthName = date('F', mktime(0, 0, 0, $month, 1)); // Get full month name
            $months[] = [
                'id' => $month,
                'name' => $monthName,
            ];
        }

        return $months;
    }

    public function years(int $start){
        $currentYear = date('Y');
        $startYear = $start;
        return range($startYear, $currentYear);
    }

    public function responseMessage(String $message){
        return response()->json(["message"=>$message]);
    }

    public function getDatesOfMonth($year, $month) {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = new \DateTime("last day of $year-$month");

        $dates = [];

        while ($startDate <= $endDate) {
            $dates[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }

        return $dates;
    }

}
