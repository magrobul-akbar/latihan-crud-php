<?php 

// config
require_once '../config.php';
    // jika pencarian dilakukan.
if( isset($_GET['cari']) ) {
    $cari = $_GET['cari'];
    $like = "WHERE nim LIKE '%$cari%' OR mahasiswa.nama LIKE '%$cari%' OR alamat LIKE '%$cari%' OR program_studi.nama LIKE '%$cari%'";
    $query = "SELECT mahasiswa.*, program_studi.nama as program_studi
                FROM mahasiswa
                LEFT JOIN program_studi
                ON program_studi.id = mahasiswa.program_studi_id
                $like
                ORDER BY mahasiswa.id DESC";
} else {
    $query = "SELECT mahasiswa.*, program_studi.nama as program_studi
                FROM mahasiswa
                LEFT JOIN program_studi
                ON program_studi.id = mahasiswa.program_studi_id
                ORDER BY mahasiswa.id DESC";
}

$mahasiswa = mysqli_query($con, $query);

$nomor = 1;
if( $mahasiswa->num_rows > 0 ) :
    while($row = $mahasiswa->fetch_object()) :
?>
<tr>
    <td><?= $nomor; ?></td>
    <td><?= $row->nim; ?></td>
    <td><?= $row->nama; ?></td>
    <td><?= $row->program_studi; ?></td>
    <td><?= $row->alamat; ?></td>
    <td>
        <a href="tambah.php?id=<?= $row->id; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        <form action="<?= BASEURL; ?>/index.php" method="post" class="d-inline-block">
            <input type="hidden" name="id" value="<?= $row->id; ?>">
            <input type="hidden" name="nim" value="<?= $row->nim; ?>">
            <button type="submit" class="btn btn-danger btn-sm"
                onclick="return confirm('Anda yakin ingin menghapus data mahasiswa dengan NIM <?= $row->nim; ?>??')"><i
                    class="bi bi-trash"></i> Hapus</button>
        </form>
    </td>
</tr>
<?php 
$nomor++;
    endwhile;
else :
?>
<tr>
    <td colspan="6" align="center">Data mahasiswa tidak ditemukan</td>
</tr>
<?php endif; ?>