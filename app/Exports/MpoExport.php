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
    protected $mpo;

    public function __construct($mpo)
    {
        $this->mpo = $mpo;
    }

    public function view(): View
    {
        return view('agency.mpo.export', [
            'time_belts' => $this->mpo['time_belts'],
            'day_numbers' =>$this->mpo['day_numbers'],
            'mpo_details' => $this->mpo['mpo_details'],
            'previous_reference' => $this->mpo['previous_reference'],
            'time_belt_summary' => $this->mpo['time_belt_summary'],
            'total_budget' => $this->mpo['total_budget'],
            'net_total' => $this->mpo['net_total']
        ]);
    }

    public function registerEvents(): array
    {
        if ($this->mpo['company']->logo) {
            $path = $this->storeFileInTmp($this->mpo['company']->logo);
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
        } else {
            return [];
        }
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
