<?php

// Include PhpSpreadsheet and define namespaces
require 'vendor/autoload.php'; // Ensure this path is correct

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

if (isset($_POST['xport']) && $_POST['xport'] === 'excel') {
    // Initialize Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set sheet properties
    $sheet->setTitle('Users Favourite List');
    $sheet->getDefaultStyle()->getFont()->setSize(11)->setName('Arial');
    $sheet->getStyle('A1:F1')->getFont()->setBold(true);

    // Apply Column Autosizing
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Apply styles to the header row
    $styleArray = [
        'font' => [
            'name' => 'Arial',
            'size' => 11,
            'bold' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFFFFFCC'],
        ],
    ];
    $sheet->getStyle('A1:F1')->applyFromArray($styleArray);

    // Sample Data Population
    $sheet->fromArray(
        [
            ['User Type', 'User Name', 'Designation', 'Region', 'Posting', 'Total Request'], // Header
            ['Admin', 'John Doe', 'Manager', 'Headquarters', 'IT Dept', 15], // Example Row 1
            ['User', 'Jane Smith', 'Analyst', 'Region 1', 'Finance', 10], // Example Row 2
        ],
        NULL,
        'A1'
    );

    // Set output file properties
    $fileName = 'UserReport.xlsx';
    $writer = new Xlsx($spreadsheet);

    // Save to a file and output
    $filePath = 'dist/' . $fileName;
    $writer->save($filePath);

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}