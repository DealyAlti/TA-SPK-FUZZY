<?php

namespace App\Exports;

use App\Models\Produk;
use App\Models\DataTraining;
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
        $produk  = Produk::orderBy('nama_produk')->get();
        $tanggal = $this->tanggal;

        // Ambil stok terakhir sebelum tanggal template
        $lastTrainingByProduk = DataTraining::select('id_produk', 'stok_barang_jadi', 'tanggal', 'id_data_training')
            ->where('tanggal', '<', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->get()
            ->unique('id_produk')
            ->keyBy('id_produk');

        $rows = [];

        // Row 1: Tanggal
        $rows[] = ['Tanggal :', $tanggal, '', '', ''];

        // Row 2: Panduan
        $panduan =
            "PANDUAN PENGISIAN:\n" .
            "1) Isi Penjualan & Hasil Produksi dalam satuan KG.\n" .
            "2) Wajib angka bulat (tanpa koma/titik). Contoh benar: 0, 5, 12, 100.\n" .
            "3) Jika tidak ada transaksi hari itu, isi 0.\n" .
            "4) Jangan ubah kolom Produk / Stok Terakhir / Tanggal.";

        $rows[] = ['Panduan :', $panduan, '', '', ''];

        // Row 3: Header
        $rows[] = ['Produk', 'Stok (KG)', 'Penjualan (KG)', 'Hasil Produksi (KG)', 'ID_PRODUK'];

        // Data rows
        foreach ($produk as $p) {
            $last = $lastTrainingByProduk->get($p->id_produk);
            $stokTerakhir = $last ? (int) $last->stok_barang_jadi : (int) ($p->stok ?? 0);

            $rows[] = [
                $p->nama_produk,
                $stokTerakhir,
                0,
                0,
                $p->id_produk
            ];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // =========================
                // COLUMN WIDTHS
                // =========================
                $sheet->getColumnDimension('A')->setWidth(32);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(24);
                $sheet->getColumnDimension('E')->setVisible(false);

                // =========================
                // MERGE & ROW HEIGHT (PANDUAN)
                // =========================
                $sheet->mergeCells('B2:D2');
                $sheet->getRowDimension(2)->setRowHeight(95);

                // =========================
                // BOLD
                // =========================
                $sheet->getStyle('A1')->getFont()->setBold(true);
                $sheet->getStyle('A2')->getFont()->setBold(true);
                $sheet->getStyle('A3:D3')->getFont()->setBold(true);

                // =========================
                // ALIGNMENT PANDUAN (LABEL & ISI)
                // =========================

                // "Panduan :" (A2)
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Isi panduan (B2:D2)
                $sheet->getStyle('B2')->getAlignment()->setWrapText(true);
                $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('B2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // =========================
                // VISUAL BOX PANDUAN
                // =========================
                $sheet->getStyle('A2:D2')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'F9FAFB'],
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ]);

                // =========================
                // HEADER STYLE
                // =========================
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FEE2E2'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // =========================
                // TABLE BORDERS
                // =========================
                $sheet->getStyle("A3:D{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ]
                    ]
                ]);

                // =========================
                // FREEZE HEADER
                // =========================
                $sheet->freezePane('A4');

                // =========================
                // PROTECTION
                // =========================
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('apn');

                // Lock all
                $sheet->getStyle("A1:E{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);

                // Unlock input columns (C & D)
                $sheet->getStyle("C4:D{$lastRow}")
                    ->getProtection()
                    ->setLocked(Protection::PROTECTION_UNPROTECTED);

                // Center numeric columns
                $sheet->getStyle("B4:D{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
