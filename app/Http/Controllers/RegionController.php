<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Region;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
    public function store(Request $request)
    {
        // Log::info('Dữ liệu nhận được:', $request->all());
    
        $validated = $request->validate([
            'name' => 'required|string',
            'soilType' => 'required|string',
            'manager' => 'required|integer',
            'coordinates' => 'required|array',
            'color' => 'required|string|unique:regions,color',
            'info' => 'required'
        ]);
    
        // Log::info("Thông tin validate:", $validated);
    
        foreach ($validated['coordinates'] as $coord) {
            if (!isset($coord['lat']) || !isset($coord['lng'])) {
                return response()->json(['error' => 'Dữ liệu tọa độ không hợp lệ!'], 400);
            }
        }
    
        $region = Region::create([
            'name' => $validated['name'],
            'soiltype' => $validated['soilType'],
            'manager_id' => $validated['manager'],
            'coordinates' => json_encode($validated['coordinates']),
            'color' => $validated['color'],
            'info' => $validated['info'],
        ]);
    
        // Log::info("Thông tin đã lưu: " . json_encode($region->toArray()));
    
        return response()->json(['message' => 'Lưu thành công!', 'region' => $region], 201);
    }
    
}
