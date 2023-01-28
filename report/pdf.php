<?php 

// config
require_once '../config.php';

// jika user belum login arahkan kehalaman login.
if( !is_login() ) {
    header("Location: ../login.php");
    return;
}

// ambil semua data mahasiswa
$mahasiswa = mysqli_query($con, "SELECT mahasiswa.*, program_studi.nama as program_studi
                                FROM mahasiswa
                                LEFT JOIN program_studi
                                ON program_studi.id = mahasiswa.program_studi_id
                                ORDER BY mahasiswa.nama ASC");

// tcpdf
require_once '../vendor/tcpdf/tcpdf.php';

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Magrobul Akbar');
$pdf->setTitle('Daftar Mahasiswa');
$pdf->setSubject('Tugas CRUD PHP');
$pdf->setKeywords('tugas, CRUD, PDF, example, test, guide');

// remove header
$pdf->setPrintHeader(false);

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->setFont('dejavusans', '', 10, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// tabel data mahasiswa
$html = '<table border="1" cellpadding="4">
            <thead>
                <tr>
                    <th colspan="5" align="center">Tabel Data Mahasiswa</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Program Studi</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>';

$nomor = 1;
while( $row = $mahasiswa->fetch_object() ) {
    $html .= '<tr>
            <td>'. $nomor++ .'</td>
            <td>'. $row->nim .'</td>
            <td>'. $row->nama .'</td>
            <td>'. $row->program_studi .'</td>
            <td>'. $row->alamat .'</td>
        </tr>';
}

$html .= '</tbody>
    </table>';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('daftar-mahasiswa.pdf', 'I');