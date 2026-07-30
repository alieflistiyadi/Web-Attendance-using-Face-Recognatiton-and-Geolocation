<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $import = new SiswaImport();

        Excel::import($import, $request->file('file'));

        return back()->with([
            'success_import' => $import->success,
            'failed_import' => $import->failed,
            'errors_import' => $import->errors
        ]);

    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama Lengkap');
        $sheet->setCellValue('C1', 'Kelas');
        $sheet->setCellValue('D1', 'Kode Jurusan');
        $sheet->setCellValue('E1', 'No HP');

        $sheet->setCellValue('A2', '00123456');
        $sheet->setCellValue('B2', 'Budi Santoso');
        $sheet->setCellValue('C2', '10');
        $sheet->setCellValue('D2', 'TM');
        $sheet->setCellValue('E2', '081234567890');

        // Header bold
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Warna header abu-abu
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('D9D9D9');

        // Rata tengah header
        $sheet->getStyle('A1:E1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border
        $sheet->getStyle('A1:E2')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Lebar kolom
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(20);

        // Format TEXT untuk NIS & No HP
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('E:E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $writer = new Xlsx($spreadsheet);

        $filename = 'Template_Data_Siswa.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
}