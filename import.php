<?php
session_start();
include('configdb.php');
require_once 'vendor/autoload.php';  // Menggunakan PHPExcel atau PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

// Memeriksa apakah file telah di-upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Cek apakah file yang di-upload adalah CSV atau XLSX
    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    if ($fileType == 'csv') {
        // Proses CSV
        if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
            $rowIndex = 0;
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                // Menambahkan data ke tabel alternatif
                if ($rowIndex > 0) {  // Menghindari header CSV
                    $sql = "INSERT INTO alternatif (alternatif, k1, k2, k3, k4, k5) VALUES ('" . $data[0] . "', '" . $data[1] . "', '" . $data[2] . "', '" . $data[3] . "', '" . $data[4] . "', '" . $data[5] . "')";
                    if ($mysqli->query($sql) !== TRUE) {
                        echo "Error: " . $mysqli->error;
                    }
                }
                $rowIndex++;
            }
            fclose($handle);
        }
    } elseif ($fileType == 'xlsx') {
        // Proses XLSX
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        $rowIndex = 1;
        foreach ($sheet->getRowIterator() as $row) {
            if ($rowIndex > 1) {  // Menghindari header
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getFormattedValue();
                }

                // Menambahkan data ke tabel alternatif
                $sql = "INSERT INTO alternatif (alternatif, k1, k2, k3, k4, k5) VALUES ('" . $data[0] . "', '" . $data[1] . "', '" . $data[2] . "', '" . $data[3] . "', '" . $data[4] . "', '" . $data[5] . "')";
                if ($mysqli->query($sql) !== TRUE) {
                    echo "Error: " . $mysqli->error;
                }
            }
            $rowIndex++;
        }
    } else {
        echo "Invalid file format. Only CSV and XLSX are allowed.";
    }

    // Redirect ke halaman alternatif setelah impor berhasil
    header('Location: alternatif.php');
    exit();
}
?>
