<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapanLkeExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $data;
    protected int   $tahun;

    public function __construct(array $data, int $tahun)
    {
        $this->data  = $data;
        $this->tahun = $tahun;
    }

    public function title(): string
    {
        return 'Rekapan LKE AKIP ' . $this->tahun;
    }

    public function headings(): array
    {
        return [
            // Baris 1: judul utama (akan di-merge via styles)
            ['REKAPAN HASIL PENILAIAN LKE AKIP PROVINSI NTT TAHUN ' . $this->tahun, '', '', '', '', '', '', '', '', ''],
            // Baris 2: kosong
            ['', '', '', '', '', '', '', '', '', ''],
            // Baris 3: header kolom
            [
                'NO',
                'NAMA PERANGKAT DAERAH',
                'PERENCANAAN KINERJA',
                'PENGUKURAN KINERJA',
                'PELAPORAN KINERJA',
                'EVALUASI AKIP INTERNAL',
                'JUMLAH NILAI',
                'KUALITAS',
                'PREDIKAT',
                'PERINGKAT',
            ],
        ];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data as $item) {
            $rows[] = [
                $item['peringkat'],
                $item['nama'],
                number_format($item['nilai1'], 2),
                number_format($item['nilai2'], 2),
                number_format($item['nilai3'], 2),
                number_format($item['nilai4'], 2),
                number_format($item['total'],  2),
                $item['predikat']['label'],
                $item['predikat']['kode'],
                $item['peringkat'],
            ];
        }
        return $rows;
    }

    public function styles(Worksheet $sheet): void
    {
        $totalRows = count($this->data);
        $lastRow   = $totalRows + 3; // 3 baris header

        // ── Merge judul ──────────────────────────────────────
        $sheet->mergeCells('A1:J1');
        $sheet->getCell('A1')->setValue('REKAPAN HASIL PENILAIAN LKE AKIP PROVINSI NTT TAHUN ' . $this->tahun);

        // ── Style judul ──────────────────────────────────────
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // ── Style header kolom (baris 3) ─────────────────────
        $sheet->getStyle('A3:J3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders'   => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']],
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(40);

        // ── Style data ───────────────────────────────────────
        if ($totalRows > 0) {
            $sheet->getStyle('A4:J' . $lastRow)->applyFromArray([
                'font'      => ['size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                ],
            ]);

            // Nama OPD left-align
            $sheet->getStyle('B4:B' . $lastRow)->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Warna baris selang-seling
            for ($i = 4; $i <= $lastRow; $i++) {
                $bg = ($i % 2 == 0) ? 'F8FAFC' : 'FFFFFF';
                $sheet->getStyle('A' . $i . ':J' . $i)
                      ->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setRGB($bg);
            }

            // Warna kolom Predikat (kode) sesuai nilai
            for ($i = 4; $i <= $lastRow; $i++) {
                $kode = $sheet->getCell('I' . $i)->getValue();
                $color = match($kode) {
                    'AA' => '059669',
                    'A'  => '2563EB',
                    'BB' => '7C3AED',
                    'B'  => 'D4982E',
                    'CC' => 'F59E0B',
                    'C'  => 'DC2626',
                    'D'  => '991B1B',
                    default => '6B7280',
                };
                $sheet->getStyle('I' . $i)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                ]);
            }
        }

        // ── Lebar kolom ──────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(14);
        $sheet->getColumnDimension('J')->setWidth(10);

        // ── Footer ───────────────────────────────────────────
        $footerRow = $lastRow + 2;
        $sheet->mergeCells('A' . $footerRow . ':J' . $footerRow);
        $sheet->getCell('A' . $footerRow)
              ->setValue('Dicetak pada: ' . now()->translatedFormat('d F Y H:i') . ' WIB');
        $sheet->getStyle('A' . $footerRow)->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
    }
}