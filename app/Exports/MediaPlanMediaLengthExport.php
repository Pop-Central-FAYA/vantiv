<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Vanguard\Services\Export\AddImageToSpreadSheet; 

class MediaPlanMediaLengthExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
	protected $summary;
    protected $media_type;
    protected $material_length;
    protected $station_programs;
    protected $monthly_weeks;
    protected $media_plan_data;
    
    public function __construct($media_type, $material_length, $station_programs, $monthly_weeks, $summary, $media_plan_data)
    {
        $this->summary = $summary;
        $this->media_type = $media_type;
        $this->material_length = $material_length;
        $this->station_programs = $station_programs;
        $this->monthly_weeks = $monthly_weeks;
        $this->media_plan_data = $media_plan_data;
    }

    public function view(): View
    {
        return view('agency.mediaPlan.export.mediaDuration', [
            'data' => collect($this->station_programs),
            'national_stations' => $this->groupByStationType($this->station_programs, 'network'),
            'cable_stations' => $this->groupByStationType($this->station_programs, 'cable'),
            'regional_stations' => $this->groupByRegions($this->station_programs),
            'monthly_weeks' => json_decode($this->monthly_weeks),
            'media_plan_data' => $this->media_plan_data,
            'media_type' => $this->media_type,
            'material_length' => $this->material_length
        ]);
    }


    public function groupByStationType($suggestions, $station_type)
    {
        $suggestions = $suggestions->where('station_type', $station_type);
        return $suggestions->groupBy('station');
    }

    public function groupByRegions($suggestions)
    {
        $suggestions = $suggestions->where('station_type', 'terrestrial');
        return $suggestions->groupBy(['station_region', 'station']);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->media_type.' '.$this->material_length.'"';
    }

    public function registerEvents(): array
    {
        $spread_sheet_image = new AddImageToSpreadSheet($this->media_plan_data->client->company_logo);
        $path = $spread_sheet_image->run();
        $style = $spread_sheet_image->styleArray();
        return [ 
            AfterSheet::class => function(AfterSheet $event) use($path, $style) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($path);
                $drawing->setCoordinates('A1');
                $drawing->setHeight(65);
                $drawing->setWidth(55);
                $drawing->setWorksheet($event->sheet->getDelegate());
                $event->sheet->getDelegate()->getStyle('B3:G3')->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle('A4')->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle('A6')->applyFromArray($style);
                $event->sheet->getDelegate()->getStyle('A7')->applyFromArray($style);
            },
        ];
    }
}