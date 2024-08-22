<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Components;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ComponentController extends Controller
{
    public function list(Request $request){
        $components = Components::all();
        $assets = Asset::all();
        return response()->json(['components' => $components, 'assets' => $assets]);
    }

    public function store(Request $request) {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255|unique:components',
            'asset_id' => 'integer',
        ]);

        // Create a new  record
        $component = Components::create([
            'name' => $validatedData['name'],
            'asset_id' => $validatedData['asset_id'],
        ]);

        return response()->json(['message' => 'Components created successfully', 'component' => $component]);

    }

    public function update(Request $request, $id) {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'asset_id' => 'integer',
        ]);

        $componentExists = Components::where('name', $validatedData['name'])->where('id', '!=', $id)->exists();

        if($componentExists) {
            throw new BadRequestHttpException('Component\'s Name Already Exists');
        }
        $component = Components::find($id);

        $component->update([
            'name' => $validatedData['name'],
            'asset_id' => $validatedData['asset_id'],
        ]);

        return response()->json(['message' => 'Component updated successfully', 'component' => $component]);

    }

    public function delete($id) {
        $exists = Maintenance::where('component_id', $id)->exists();

        if($exists)
            throw new BadRequestHttpException('Component is used by maintenance.');

        Components::find($id)->delete();

        return response()->json(['message' => 'Component deleted successfully']);

    }
}
