<?php

namespace App\Http\Controllers;

use App\Models\SensorData;

use Illuminate\Http\Request;
use App\Exports\SensorExport;
use Maatwebsite\Excel\Facades\Excel;

class SensorController extends Controller
{
    public function index()
    {
        $data = SensorData::latest()->first();
        return view('pages.dashboard', compact('data'));
    }

    public function getRealtimeData()
    {
        $data = SensorData::latest()->take(10)->get()->reverse()->values();

        return response()->json($data);
    }

    public function exportSensor(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $fileName = "sensor_log_{$startDate}_to_{$endDate}.xlsx";

        return Excel::download(new SensorExport($startDate, $endDate), $fileName);
    }

    public function store(Request $request)
    {
        SensorData::create([
            'humidity' => $request->humidity,
            'temperature' => $request->temperature,
            'soil_moisture' => $request->soil_moisture,
            'rain_status' => '0',
        ]);

        return response()->json(['message' => 'Data sensor berhasil disimpan.'], 201);
    }

    public function log_sensor()
    {
        return view('pages.log_sensor.index');
    }

    public function data_sensor()
    {
        $data = SensorData::latest()->paginate(10);
        return response()->json($data);
    }
}
