<?php

namespace App\Exports;

use App\Models\Produk;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TrainingHarianTemplateExport implements FromArray, WithEvents
{
    protected string $tanggal;

    public function __construct(string $tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function array(): array
    {
        $produk = Produk::orderBy('nama_produk')->get();

        $rows = [];
        $rows[] = ['Tanggal :', $this->tanggal, '', ''];               // A1,B1
        $rows[] = ['Produk', 'Penjualan', 'Hasil Produksi', 'ID_PRODUK']; // header

        foreach ($produk as $p) {
            $rows[] = [$p->nama_produk, 0, 0, $p->id_produk]; // kolom D hidden (id)
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // width
                $sheet->getColumnDimension('A')->setWidth(28);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(16);
                $sheet->getColumnDimension('D')->setVisible(false); // hide id

                // style header
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A2:C2')->getFont()->setBold(true);

                $sheet->getStyle('A2:C2')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FEE2E2'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // border table
                $sheet->getStyle("A2:C{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ]
                    ]
                ]);

                // freeze header
                $sheet->freezePane('A3');

                // protect sheet: lock A + header, unlock B1 + B3:C...
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('apn');

                $sheet->getStyle("A1:D{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);

                // unlock tanggal
                $sheet->getStyle('B1')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

                // unlock input
                $sheet->getStyle("B3:C{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_UNPROTECTED);

                $sheet->getStyle("B3:C{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
