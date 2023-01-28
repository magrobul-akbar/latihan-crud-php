<?php 

// config
require_once 'config.php';

// jika user belum login arahkan kehalaman login.
if( !is_login() ) {
    header("Location: login.php");
    return;
}

// jika tombol hapus diklik.
if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $delete = mysqli_query($con, "DELETE FROM mahasiswa WHERE id = '$id'");

    if( $delete ) {
        $_SESSION['success'] = "Data mahasiswa dengan NIM $nim berhasil dihapus";
        header("Location: index.php");
        return;
    } else {
        die(mysqli_error($con));
    }
}

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
    $cari = null;
    $query = "SELECT mahasiswa.*, program_studi.nama as program_studi
                FROM mahasiswa
                LEFT JOIN program_studi
                ON program_studi.id = mahasiswa.program_studi_id
                ORDER BY mahasiswa.id DESC";
}

// ambil semua data mahasiswa
$mahasiswa = mysqli_query($con, $query);

// header
$title = 'Mahasiswa';
require_once 'layout/header.php';
?>

<!-- pesan success -->
<?php if( isset( $_SESSION['success'] ) ) : ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success']; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php unset($_SESSION['success']); endif; ?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title m-0 p-0"><i class="bi bi-list-columns"></i> Tabel Data Mahasiswa</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 tools">
            <div class="d-flex align-items-end">
                <a href="tambah.php" class="btn btn-primary"><i class="bi bi-plus"></i> Tambah Data</a>

                <?php if( $mahasiswa->num_rows > 0 ) : ?>
                <a href="#" onclick="window.print()" class="btn btn-secondary btn-sm ml-1"><i class="bi bi-printer"></i>
                    Print</a>
                <a href="report/excel.php" target="_blank" class="btn btn-success btn-sm ml-1"><i
                        class="bi bi-file-spreadsheet"></i> Excel</a>
                <a href="report/pdf.php" target="_blank" class="btn btn-danger btn-sm ml-1"><i
                        class="bi bi-file-pdf"></i> PDF</a>
                <?php endif; ?>

            </div>

            <?php if( $mahasiswa->num_rows > 0 ) : ?>
            <form action="<?= $_SERVER['PHP_SELF']; ?>" autocomplete="off" class="d-flex">
                <input type="text" class="form-control mr-1" name="cari" id="cari" placeholder="Cari data mahasiswa"
                    value="<?= $cari; ?>">
                <button type="submit" class="btn btn-success" id="cariBtn"><i class="bi bi-search"></i></button>
            </form>
            <?php endif; ?>

        </div>


        <h5 class="card-title m-0 p-0 text-center mb-1 print-header">Tabel Data Mahasiswa</h5>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">NIM</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Program Studi</th>
                    <th scope="col">Alamat</th>
                    <th scope="col" class="aksi">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabelMahasiswa">

                <?php 
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
                    <td class="aksi">
                        <a href="tambah.php?id=<?= $row->id; ?>" class="btn btn-sm btn-warning"><i
                                class="bi bi-pencil"></i> Edit</a>
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

            </tbody>
        </table>
    </div>
</div>

<script>
const liveSearch = true;
const cari = document.querySelector('#cari');
const cariBtn = document.querySelector('#cariBtn');
const tabelMahasiswa = document.querySelector('#tabelMahasiswa');

if (liveSearch == true) {
    cariBtn.classList.add('d-none');
    cari.addEventListener('keyup', function() {
        const ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function() {
            if (ajax.readyState == 4 && ajax.status == 200) {
                tabelMahasiswa.innerHTML = ajax.responseText;
            }
        }

        // send ajax
        ajax.open('GET', '<?= BASEURL; ?>/ajax/mahasiswa.php?cari=' + this.value, true);
        ajax.send();
    });
} else {
    cariBtn.classList.remove('d-none');
}
</script>
<?php 
    // footer
    require_once 'layout/footer.php';
?>