<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Vanguard\Services\Export\AddImageToSpreadSheet; 

class MediaPlanSummaryExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    protected $summary;
    protected $media_plan_data;
    
    public function __construct($summary, $media_plan_data)
    {
        $this->summary = $summary;
        $this->media_plan_data = $media_plan_data;
    }

    public function view(): View
    {
        return view('agency.mediaPlan.export.summary', [
            'summary' => collect($this->summary)
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Media Plan Summary";
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
            },
        ];
    }
}