<?php

namespace App\Http\Controllers;

use App\Http\Helpers\NumberToLetter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
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


    /**
     * Returns a json response with success true and the message.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    public function fsSuccess(?string $message): JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message
        ]);
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

    public function getDatesIntervale($start_date, $end_date, $month,$year ) {
        $startDate = new \DateTime($start_date);
        $endDate = new \DateTime($end_date);

        $month_endDate = new \DateTime("last day of $year-$month");
        $month_startDate =  new \DateTime("$year-$month-01");

        $dates = [];

        if($startDate < $month_startDate )
            $startDate = $month_startDate;

        if(!$end_date) {
            $endDate = $month_endDate;
        } else {
            if($endDate > $month_endDate)
                $endDate = $month_endDate;
        }

        while ($startDate <= $endDate) {
            $dates[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }

        return $dates;
    }


    function get_dates_between($start_date_str, $end_date_str) {
        $start_date = new \DateTime($start_date_str);
        $end_date = new \DateTime($end_date_str);

        $dates = array();
        $current_date = $start_date;
        while ($current_date <= $end_date) {
            $dates[] = $current_date->format('Y-m-d');
            $current_date->add(new \DateInterval('P1D'));
        }

        return $dates;
    }



}
