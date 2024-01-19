<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\Product;
use App\Models\ProductBenefit;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BenefitController extends Controller
{
    //
    public function getBenefits(Request $request){
        $searchValue = $request->input('searchValue', ''); // search value
        $perPage = $request->input('perPage', 10); // Default per page value is 10 if not provided
        $currentPage = $request->input('currentPage', 1); // Default current page value is 1 if not provided


        $benefits = Benefit::paginate($perPage, ['*'], 'page', $currentPage);
        $totalBenefits = $benefits->total(); // Total number of payments matching the query
        $totalPage = ceil($totalBenefits / $perPage); // Calculate total pages
        $months = $this->months(1);
        $years = $this->years(2000);

        return response()->json(["benefits" => $benefits, "totalPage" => $totalPage, "totalBenefits"=>$totalBenefits, "months"=>$months, "years"=>$years]);

    }

    public function getArticlesBenefit(Request $request, $Id) {
        $articles = ProductBenefit::with('product')->where('benefit_id',$Id)->get();
        return response()->json(['articles' => $articles]);

    }

    public function store(Request $request): JsonResponse
    {

        $data = $request->input('data');
        $month = $data['month'];
        $year = $data['year'];
        $raw_material_price = 0;


        $benefit = Benefit::where('month',$month)->where('year',$year)->get();

        if(count($benefit) > 0) {
            throw new BadRequestHttpException('This Benefit Already Exists');
        }

        $Newbenefit = Benefit::create(['month'=>$month, 'year'=>$year, 'benefit'=>0, 'raw_material_price'=>$raw_material_price]);

        $products = SaleItem::whereMonth('sale_date',1)->whereYear('sale_date',2024)->pluck('product_id');
        foreach ($products as $product) {
            $_product =  Product::find($product);
            ProductBenefit::create([
                'benefit_id' => $Newbenefit->id,
                'product_id' => $product,
                'raw_material_price' => $raw_material_price,
                'weight' =>$_product->weight,
                'benefit' => 0,
                'product_price' => 0,
            ]);
        }



        return response()->json(['message' => 'Benefit created successfully']);
    }
}
