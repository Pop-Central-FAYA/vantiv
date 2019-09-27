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
        $company_logo = $this->media_plan_data->company->logo;
        if ($company_logo) {
            $spread_sheet_image = new AddImageToSpreadSheet($company_logo);
            $path = $spread_sheet_image->run();
            $style = $spread_sheet_image->styleArray();
            return [ 
                AfterSheet::class => function(AfterSheet $event) use($path, $style) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo');
                    $drawing->setPath($path);
                    $drawing->setCoordinates('B1');
                    $drawing->setResizeProportional(false);
                    $drawing->setWidth(140);
                    $drawing->setHeight(70);
                    $drawing->setWorksheet($event->sheet->getDelegate());

                    $styleArrayAllBorders = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            ],
                        ],
                    ];
                    $event->sheet->getColumnDimension('B')->setAutoSize(false);
                    $event->sheet->getColumnDimension('B')->setWidth(15);
                    $event->sheet->getColumnDimension('C')->setAutoSize(false);
                    $event->sheet->getColumnDimension('C')->setWidth(25);
                    $event->sheet->getStyle('B5:'.$event->sheet->getHighestColumn(). ($event->sheet->getHighestRow() - 2))->applyFromArray($styleArrayAllBorders);
                    $event->sheet->getStyle('B4:G4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('b31b1b');
                    $event->sheet->getStyle('B4:G4')->getFont()->getColor()->setARGB('ffffff');
                },
            ];
        } else {
            return [];
        }
    }
}