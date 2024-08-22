<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Components;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AssetController extends Controller
{
    public function list(Request $request){
        $assets = Asset::all();
        return response()->json(['assets' => $assets]);
    }
    public function getComponents($asset_id){
        $components = Components::where('asset_id', $asset_id)->get();
        return response()->json(['components' => $components]);
    }

    public function store(Request $request) {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255|unique:assets',
        ]);

        // Create a new  record
        $asset = Asset::create([
            'name' => $validatedData['name'],
        ]);

        return response()->json(['message' => 'Asset created successfully', 'asset' => $asset]);

    }

    public function update(Request $request, $id) {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
        ]);

        $assetExists = Asset::where('name', $validatedData['name'])->where('id', '!=', $id)->exists();

        if($assetExists) {
            throw new BadRequestHttpException('Asset\'s Name Already Exists');
        }
        $asset = Asset::find($id);

        $asset->update([
            'name' => $validatedData['name'],
        ]);

        return response()->json(['message' => 'Asset updated successfully', 'asset' => $asset]);

    }

    public function delete($id) {

        $existsMaintenance = Maintenance::where('asset_id', $id)->exists();
        $existsComponent = Components::where('asset_id', $id)->exists();


        if($existsMaintenance || $existsComponent){
            throw new BadRequestHttpException('Asset is used by maintenance.');
        }


        Asset::find($id)->delete();

        return response()->json(['message' => 'Asset deleted successfully']);

    }
}
