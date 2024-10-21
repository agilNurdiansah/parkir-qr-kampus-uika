<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Resources\VehicleResource;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function vehicleEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $vehicle = Vehicle::create([
            'vehicle_number' => $request->vehicle_number,
            'status' => 'in',
            'entry_time' => now(),
        ]);

        return response()->json([
            'status' => 'Success',
            'message' => 'Vehicle entered',
            'data' => $vehicle
        ], 201);
    }

    public function vehicleExit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $vehicle = Vehicle::where('vehicle_number', $request->vehicle_number)
            ->where('status', 'in')
            ->first();

        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found or already exited'], 404);
        }

        $vehicle->update([
            'status' => 'out',
            'exit_time' => now(),
        ]);

        return response()->json([
            'status' => 'Success',
            'message' => 'Vehicle exited',
             'data' => $vehicle
            ], 200);
    }

    public function checkVehicleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $vehicle = Vehicle::where('vehicle_number', $request->vehicle_number)->first();

        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Vehicle status retrieved',
            'data' => [
                'vehicle_number' => $vehicle->vehicle_number,
                'status' => $vehicle->status,
                'entry_time' => $vehicle->entry_time,
                'exit_time' => $vehicle->exit_time,
            ]
        ], 200);
    }

    public function vehicleList(Request $request)
    {
        $perPage = 5;
        $vehicles = Vehicle::paginate($perPage);

        return response()->json([
            'status' => 'Success',
            'message' => 'List of vehicles',
            'data' => VehicleResource::collection($vehicles),
            'current_page' => $vehicles->currentPage(),
            'total' => $vehicles->total(),
            'per_page' => $vehicles->perPage(),
        ], 200);
    }

}
