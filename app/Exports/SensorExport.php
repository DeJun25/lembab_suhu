<?php

namespace App\Exports;

use App\Models\SensorData;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SensorExport implements FromQuery, WithHeadings, ShouldAutoSize, WithStyles, WithMapping
{
    protected $start_date;
    protected $end_date;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->start_date = $startDate;
        $this->end_date = $endDate;
    }

    public function query()
    {
        $query = SensorData::query();

        if ($this->start_date && $this->end_date) {
            $query->whereDate('created_at', '>=', $this->start_date)
                  ->whereDate('created_at', '<=', $this->end_date);
        }

        return $query->latest();
    }

    public function map($item): array
    {
        return [
            $item->created_at->format('Y-m-d H:i:s'),
            $item->temperature . ' °C',
            $item->humidity . ' %',
            $item->soil_moisture . ' %',
        ];
    }

    public function headings(): array
    {
        return [
            'Time',
            'Temperature',
            'Humidity',
            'Soil Moisture',
            'Rain Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E2E2']
                ]
            ],
        ];
    }
}
