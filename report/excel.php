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
?>

<table border="1" cellpadding="4">
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
    <tbody>
        <?php 
            $nomor = 1;
                if( $mahasiswa->num_rows > 0 ) :
                    while($row = $mahasiswa->fetch_object()) :
        ?>
        <tr>
            <td><?= $nomor++; ?></td>
            <td><?= $row->nim; ?></td>
            <td><?= $row->nama; ?></td>
            <td><?= $row->program_studi; ?></td>
            <td><?= $row->alamat; ?></td>
        </tr>
        <?php 
                endwhile;
            endif;
        ?>
    </tbody>
</table>

<?php 
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Daftar Mahasiswa.xls");
?>