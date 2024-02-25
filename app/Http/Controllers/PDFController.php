<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

use PDF;

class PDFController extends Controller
{
    public function generateCustomerLog()
    {
        // retreive all records from db
       // $data = Employee::all();
        // share data to view
       // view()->share('employee',$data);
        $pdf = PDF::loadView('client.log.pdf', []);
        // download PDF file with download method
        return $pdf->download('laraveltuts.pdf');
    }
}
