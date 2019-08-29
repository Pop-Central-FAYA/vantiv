<?php

namespace Vanguard\Services\Export;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\UploadedFile;
use Vanguard\Services\BaseServiceInterface;

class AddImageToSpreadSheet implements BaseServiceInterface
{
    protected $image_url;

    public function __construct($image_url)
    {
        $this->image_url = $image_url;
    }

    public function run()
    {
        return $this->storeFileInTmp($this->image_url);
    }

    public function storeFileInTmp($url)
    {
        $info = pathinfo($url);
        $contents = file_get_contents($url);
        $file = '/tmp/' . $info['basename'];
        file_put_contents($file, $contents);
        $uploaded_file = new UploadedFile($file, $info['basename']);
        return realPath($uploaded_file);
    }

    public function styleArray()
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