<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $sheet->setCellValue('A2', '*** HAPUS BARIS INI SEBELUM MENGISI DATA ***');
        $sheet->setCellValue('B2', 'Contoh: Budi Santoso');
        $sheet->setCellValue('C2', 'Contoh: 10');
        $sheet->setCellValue('D2', 'Contoh: TM');
        $sheet->setCellValue('E2', 'Contoh: 628123456789');

        $writer = new Xlsx($spreadsheet);

        $filename = 'Template_Data_Siswa.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }
}