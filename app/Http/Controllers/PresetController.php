<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PresetsRequest;
use App\Models\Preset;

class PresetController extends Controller
{
    public function index(PresetsRequest $request) {

        $data = [];

        try {
            $presetQuery = Preset::query();

            if (isset($request->sort_field) && isset($request->sort_direction)) {
                $presetQuery->orderBy($request->sort_field ?? 'id', $request->sort_direction);
            }


            $data = $presetQuery->paginate(
                perPage: $request->pagintaion_size ?? 10,
                page: $request->page ?? 1
            );
            foreach ($data as $key => $value) {
                $value->products_count = $value->getRelatedProducts()->count();
                $value->products_sum = number_format($value->getRelatedProducts()->sum('price'),2);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error_message' => $th->getMessage()
            ], 500);
        }

        return response()->json($data, 200);
    }

    public function store() {
        
    }

    public function update() {
        
    }

    public function destroy() {
        
    }
}
