<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Region;

class RegionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'soilType' => 'required|string',
            'manager' => 'required|integer',
            'coordinates' => 'required|array',
        ]);

        // Kiểm tra format từng phần tử
        foreach ($validated['coordinates'] as $coord) {
            if (!isset($coord['lat']) || !isset($coord['lng'])) {
                return response()->json(['error' => 'Dữ liệu tọa độ không hợp lệ!'], 400);
            }
        }

        // Lưu vào database với đúng format
        $region = Region::create([
            'name' => $validated['name'],
            'soiltype' => $validated['soilType'],
            'manager_id' => $validated['manager'],
            'coordinates' => json_encode($validated['coordinates']), // Giữ nguyên format object
        ]);

        return response()->json(['message' => 'Lưu thành công!', 'region' => $region], 201);
    }
}
