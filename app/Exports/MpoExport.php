<?php

namespace Vanguard\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MpoExport implements FromView, WithEvents
{
    protected $exportable_mpos;
    protected $day_numbers;
    protected $mpo_details;
    protected $summary;
    protected $total_budget;
    protected $net_total;
    protected $company_logo;

    public function __construct($exportable_mpos, $day_numbers, $mpo_details, $total_budget, $net_total, $summary, $company_logo)
    {
        $this->exportable_mpos = $exportable_mpos;
        $this->day_numbers = $day_numbers;
        $this->mpo_details = $mpo_details;
        $this->total_budget = $total_budget;
        $this->net_total = $net_total;
        $this->summary = $summary;
        $this->company_logo = $company_logo;
    }

    public function view(): View
    {
        return view('agency.campaigns.export_mpo', [
            'mpos' => $this->exportable_mpos,
            'day_numbers' =>$this->day_numbers,
            'mpo_details' => $this->mpo_details,
            'time_belt_summary' => $this->summary,
            'total_budget' => $this->total_budget,
            'net_total' => $this->net_total
        ]);
    }

    public function registerEvents(): array
    {
        $path = $this->storeFileInTmp($this->company_logo);
        return [ 
            AfterSheet::class => function(AfterSheet $event) use($path) {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo');
                $drawing->setPath($path);
                $drawing->setCoordinates('A1');
                $drawing->setHeight(65);
                $drawing->setWidth(55);
                $drawing->setWorksheet($event->sheet->getDelegate());
                $event->sheet->getDelegate()->getStyle('B3:G3')->applyFromArray($this->styleArray());
                $event->sheet->getDelegate()->getStyle('A4')->applyFromArray($this->styleArray());
                $event->sheet->getDelegate()->getStyle('A6')->applyFromArray($this->styleArray());
                $event->sheet->getDelegate()->getStyle('A7')->applyFromArray($this->styleArray());
            },
        ];
    }

    private function storeFileInTmp($url)
    {
        $info = pathinfo($url);
        $contents = file_get_contents($url);
        $file = '/tmp/' . $info['basename'];
        file_put_contents($file, $contents);
        $uploaded_file = new UploadedFile($file, $info['basename']);
        return realPath($uploaded_file);
    }

    private function styleArray()
    {
        return [
                'font' => [
                    'name' => 'Times New Roman',
                    'bold' => true,
                    'italic' => false,
                    'strikethrough' => false,
                    'size' => 13
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DASHDOT,
                        'color' => [
                            'rgb' => '808080'
                        ]
                    ],
                    'top' => [
                        'borderStyle' => Border::BORDER_DASHDOT,
                        'color' => [
                            'rgb' => '808080'
                        ]
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'quotePrefix'    => true
            ];
    }
}