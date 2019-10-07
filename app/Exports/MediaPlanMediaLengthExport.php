<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MediaPlanMediaLengthExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    protected $media_type;
    protected $duration;
    protected $stations_programs;
    protected $formated_plan;

    public function __construct($media_type, $duration, $stations_programs, $formated_plan)
    {
        $this->media_type = $media_type;
        $this->duration = $duration;
        $this->stations_programs = $stations_programs;
        $this->formated_plan = $formated_plan;
    }

    public function view(): View
    {
        return view('agency.mediaPlan.export.mediaDuration', [
            'station_type_data' => collect($this->stations_programs),
            'monthly_weeks' => $this->formated_plan['table_header_monthly_weeks'],
            'media_plan_data' => $this->formated_plan['plan'],
            'media_type' => $this->media_type,
            'material_length' => $this->duration,
            'media_plan_period' => $this->formated_plan['period'],
            'brand_color' => $this->getBrandColor()
        ]);
    }

    public function getBrandColor() {
        return 'b31b1b';
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return strtoupper($this->media_type.' '.$this->duration.'"');
    }

    public function registerEvents(): array
    {
        return [ 
            AfterSheet::class => function(AfterSheet $event) {
                $styleArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                    ],
                ];

                $styleArrayNoBorder= [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('rgb' => 'ffffff'),
                        ],
                        'vertical' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('rgb' => 'ffffff'),
                        ],
                    ],
                ];

                $styleArrayInsideBorder= [
                    'borders' => [
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('rgb' => 'ffffff'),
                        ],
                    ],
                ];

                $styleArrayNoHorizontalBorder= [
                    'borders' => [
                        'horizontal' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('rgb' => 'ffffff'),
                        ],
                    ],
                ];

                $styleArrayVerticalBorder= [
                    'borders' => [
                        'vertical' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                    ],
                ];

                $styleArrayAllBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                    ],
                ];

                $styleArrayTopBorder = [
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ]
                ];

               $event->sheet->getStyle('A1:'.$event->sheet->getHighestColumn(). $event->sheet->getHighestRow())->getFont()->setSize(14);
               $event->sheet->getStyle('A1:'.$event->sheet->getHighestColumn().'1')->applyFromArray($styleArrayNoBorder);
               $event->sheet->getStyle('A1:A11')->applyFromArray($styleArrayNoHorizontalBorder);
               $event->sheet->getStyle('B2:'.$event->sheet->getHighestColumn(). $event->sheet->getHighestRow())->applyFromArray($styleArray);
               $event->sheet->getStyle('B2:'.$event->sheet->getHighestColumn().'9')->applyFromArray($styleArrayInsideBorder);
               $event->sheet->getColumnDimension('A')->setAutoSize(false);
               $event->sheet->getColumnDimension('A')->setWidth(30);
               $event->sheet->getColumnDimension('C')->setAutoSize(false);
               $event->sheet->getColumnDimension('C')->setWidth(4);
               $event->sheet->getColumnDimension('E')->setAutoSize(false);
               $event->sheet->getColumnDimension('E')->setWidth(4);
               $event->sheet->getStyle('B10:'.$event->sheet->getHighestColumn().'11')->applyFromArray($styleArrayAllBorder);
               $event->sheet->getStyle('B10:'.$event->sheet->getHighestColumn().'10')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($this->getBrandColor());
               $event->sheet->getStyle('B10:'.$event->sheet->getHighestColumn().'10')->getFont()->getColor()->setARGB('ffffff');

               $event->sheet->getStyle('I15:I'.$event->sheet->getHighestRow())->getFont()->getColor()->setARGB('b31b1b');
               $event->sheet->getStyle('S15:S'.$event->sheet->getHighestRow())->getFont()->getColor()->setARGB('b31b1b');

               $event->sheet->getStyle('B12:T13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('B4B2B2');
               $event->sheet->getStyle($event->sheet->getHighestColumn().'12:'.$event->sheet->getHighestColumn().'13')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('B4B2B2');
               $event->sheet->getStyle('B12:'.$event->sheet->getHighestColumn(). $event->sheet->getHighestRow())->applyFromArray($styleArrayVerticalBorder);
               $event->sheet->getStyle('B12:'.$event->sheet->getHighestColumn(). $event->sheet->getHighestRow())->getAlignment()->setHorizontal('center');
               $event->sheet->getStyle('C13:I13')->applyFromArray($styleArrayTopBorder);
               $event->sheet->getStyle('C13:I13')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
               $event->sheet->getStyle('C13:I13')->getAlignment()->setHorizontal('center');
               $event->sheet->getStyle('A1:A'.$event->sheet->getHighestRow())->getAlignment()->setHorizontal('center');

               $event->sheet->getStyle('P12:Q'.$event->sheet->getHighestRow())->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff3b4');
               $event->sheet->getStyle('T12:T'.$event->sheet->getHighestRow())->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fff3b4');

               $event->sheet->getStyle('B'.$event->sheet->getHighestRow().':'.$event->sheet->getHighestColumn().$event->sheet->getHighestRow())->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($this->getBrandColor());
               $event->sheet->getStyle('B'.$event->sheet->getHighestRow().':'.$event->sheet->getHighestColumn().$event->sheet->getHighestRow())->getFont()->getColor()->setARGB('ffffff');               

            },
        ];
    }
}