<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\Product;
use App\Models\ProductBenefit;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $benefit = Benefit::find($Id);
        return response()->json(['articles' => $articles, "benefit" => $benefit]);

    }

    public function store(Request $request): JsonResponse
    {

        $data = $request->input('data');
        $month = $data['month'];
        $year = $data['year'];
        $raw_material_price = 0;

        $salesSummary = DB::table('sale_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_amount'), DB::raw('AVG(price) as avg_price'))
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->groupBy('product_id')
            ->get();



        $benefit = Benefit::where('month',$month)->where('year',$year)->get();

        if(count($benefit) > 0) {
            throw new BadRequestHttpException('This Benefit Already Exists');
        }

        $Newbenefit = Benefit::create(['month'=>$month, 'year'=>$year, 'benefit'=>0, 'raw_material_price'=>$raw_material_price]);

//        $products = SaleItem::whereMonth('sale_date',$month)->whereYear('sale_date',$year)->pluck('product_id')->unique();
        foreach ($salesSummary as $product) {
            $_product =  Product::find($product->product_id);
            ProductBenefit::create([
                'benefit_id' => $Newbenefit->id,
                'product_id' => $_product->id,
                'raw_material_price' => $raw_material_price,
                'weight' =>$_product->weight,
                'benefit' => 0,
                'product_price' => $product->avg_price,
                'quantity' => $product->total_quantity,
                'total_amount' => $product->total_amount,
            ]);
        }



        return response()->json(['message' => 'Benefit created successfully']);
    }

    public function updateBenefit(Request $request, $id): JsonResponse
    {

        $items = $request->input('data');
        $benefitObject = $request->input('benefit');
        $raw_material_price = $benefitObject['raw_material_price'];
        $total_profit =$benefitObject['total_profit'];

        foreach ($items as $item) {
            $productBenefit = ProductBenefit::find($item['id']);

            $productBenefit->update([
                'raw_material_price' => floatval($raw_material_price),
                'benefit' => floatval($item['benefit']),
                'product_price' => floatval($item['product_price']),
                'total_profit' => floatval($item['total_profit']),
            ]);
        }
        $benefit = Benefit::find($id);

        $benefit->update([
            'raw_material_price' => floatval($raw_material_price),
            'benefit' => floatval($total_profit),
        ]);


        return response()->json(['message' => 'Benefit updated successfully']);
    }

    public function destroyBenefit($id){
        $benefit = Benefit::find($id);

        ProductBenefit::where('benefit_id', $id)->delete();
        $benefit->delete();
        return response()->json(['message' => 'Benefit deleted successfully']);

    }

    public function refreshBenefitData(Request $request, $id): JsonResponse
    {
        $this->refreshBenefitColumns($id);

        return response()->json(['message' => 'Refreshed successfully']);
    }

    public function refreshBenefitColumns($id) {
        $benefit = Benefit::find($id);

        $salesSummary = DB::table('sale_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_price) as total_amount'), DB::raw('AVG(price) as avg_price'))
            ->whereYear('sale_date', $benefit->year)
            ->whereMonth('sale_date', $benefit->month)
            ->groupBy('product_id')
            ->get();


        foreach ($salesSummary as $product) {
            $_product =  Product::find($product->product_id);

            $productBenefit = ProductBenefit::where('product_id', $product->product_id)->where('benefit_id', $benefit->id)->get();

            if(count($productBenefit) > 0 ){
                $productBenefit->first()->update([
                    'product_price' => $product->avg_price,
                    'quantity' => $product->total_quantity,
                    'total_amount' => $product->total_amount,
                ]);
            }else {
                ProductBenefit::create([
                    'benefit_id' => $benefit->id,
                    'product_id' => $product->product_id,
                    'raw_material_price' => $benefit->raw_material_price,
                    'weight' =>$_product->weight,
                    'benefit' => 0,
                    'product_price' => $product->avg_price,
                    'quantity' => $product->total_quantity,
                    'total_amount' => $product->total_amount,
                ]);
            }

        }
    }
}
