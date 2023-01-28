<?php 

// config
require_once 'config.php';

// jika user belum login arahkan kehalaman login.
if( !is_login() ) {
    header("Location: login.php");
    return;
}

// ambil data mahasiswa
if( isset($_GET['id']) ) {
    $id = $_GET['id'];
    $mahasiswa = mysqli_query($con, "SELECT * FROM mahasiswa
                                    WHERE id = '$id'
                                    LIMIT 1");
    
    // jika mahasiswa dengan id $id tidak ditemukan.
    if( $mahasiswa->num_rows == 0 ) {
        header("Location: index.php");
        return;
    }

    $mahasiswa = $mahasiswa->fetch_object();
} else {
    $mahasiswa = null;
}

// jika tombol simpan diklik.
if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $nim = htmlspecialchars($_POST['nim']);
    $nama = htmlspecialchars($_POST['nama']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $prodi = $_POST['prodi'];

    // simpan data lama.
    old('nim', $nim);
    old('nama', $nama);
    old('alamat', $alamat);
    old('prodi', $prodi);

    // validasi field.
    $errors = [];
    if( empty($nim) ) {
        $errors['nim'] = 'NIM harus diisi!';
    } elseif(!is_numeric($nim)) {
        $errors['nim'] = 'NIM harus berisi angka numeric!';
    } else if(strlen($nim) > 10) {
        $errors['nim'] = 'Panjang NIM tidak boleh lebih dari 10 angka!';
    } else {
        if( !is_null($mahasiswa) && $mahasiswa->nim == $nim ) {
            $mahasiswaNim = mysqli_query($con, "SELECT nim FROM mahasiswa WHERE nim = '$nim' AND id != '$mahasiswa->id'");
        } else {
            $mahasiswaNim = mysqli_query($con, "SELECT nim FROM mahasiswa WHERE nim = '$nim'");
        }
        
        if( $mahasiswaNim->num_rows > 0 ) {
            $errors['nim'] = 'NIM ini sudah ada!';
        }
    }

    if( empty($nama) ) {
        $errors['nama'] = 'Nama harus diisi!';
    }

    if( empty($alamat) ) {
        $errors['alamat'] = 'Alamat harus diisi!';
    }

    if( empty($prodi) ) {
        $errors['prodi'] = 'Program Studi harus dipilih!';
    }

    // kirim pesan error jika ada.
    if( count($errors) > 0 ) {
        $_SESSION['errors'] = $errors;
        header("Location: " . $_SERVER['REQUEST_URI']);
        return;
    }

    if( !is_null($mahasiswa) ) {
        // update data
        $query = "UPDATE mahasiswa SET nim = '$nim',
                                        nama = '$nama',
                                        alamat = '$alamat',
                                        program_studi_id = '$prodi'
                                    WHERE id = '$mahasiswa->id'";
        $success = 'Data mahasiswa berhasil diperbarui';

    } else {
        // simpan data
        $query = "INSERT INTO mahasiswa VALUES(
            null, '$nim', '$nama', '$prodi', '$alamat'
        )";

        $success = 'Data mahasiswa berhasil disimpan';
    }

    $simpan = mysqli_query($con, $query);
    if( $simpan ) {
        // hilangkan semua pesan error dan data lama.
        unset($_SESSION['errors']);
        unsetOld();

        // arahkan ke halaman index
        $_SESSION['success'] = $success;
        header("Location: index.php");
        return;
    } else {
        die(mysqli_error($con));
    }
}

// ambil semua data program studi
$programStudi = mysqli_query($con, "SELECT * FROM program_studi ORDER BY nama ASC");

// header
if( !is_null($mahasiswa) ) {
    $title = 'Edit Data Mahasiswa';
} else {
    $title = 'Tambah Data Mahasiswa';
}

require_once 'layout/header.php';
?>

<!-- pesan error -->
<?php if( isset($_SESSION['errors']) ) : ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <?php foreach($_SESSION['errors'] as $error) : ?>
    <strong>Error :</strong> <?= $error; ?> <br>
    <?php endforeach; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary btn-sm mb-1"
    onclick="return confirm('Anda yakin??, perubahan yang anda lakukan tidak akan disimpan.')"><i
        class="bi bi-box-arrow-left"></i> Kembali</a>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title m-0 p-0">
            <?= !is_null($mahasiswa) ? '<i class="bi bi-pencil"></i> ' . $title : '<i class="bi bi-pencil"></i> ' . $title; ?>
        </h5>
    </div>
    <div class="card-body">
        <form action="" method="post" autocomplete="off">
            <div class="form-group">
                <label for="nim">Nim</label>
                <input type="text"
                    class="form-control <?= ( isset($_SESSION['errors']['nim']) ) ? 'is-invalid' : ''; ?>"
                    placeholder="Masukan Nim" name="nim" id="nim"
                    value="<?= (!is_null($mahasiswa) && is_null(old('nim')) ) ? $mahasiswa->nim : old('nim'); ?>"
                    autofocus>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text"
                    class="form-control <?= ( isset($_SESSION['errors']['nama']) ) ? 'is-invalid' : ''; ?>"
                    placeholder="Masukan Nama" name="nama" id="nama"
                    value="<?= (!is_null($mahasiswa) && is_null(old('nama'))) ? $mahasiswa->nama : old('nama'); ?>">
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="4"
                    class="form-control <?= ( isset($_SESSION['errors']['alamat']) ) ? 'is-invalid' : ''; ?>"
                    placeholder="Masukan alamat"><?= (!is_null($mahasiswa) && is_null(old('alamat'))) ? $mahasiswa->alamat : old('alamat'); ?></textarea>
            </div>

            <div class="form-group">
                <label for="prodi">Program Studi</label>
                <select name="prodi" id="prodi"
                    class="form-control <?= ( isset($_SESSION['errors']['prodi']) ) ? 'is-invalid' : ''; ?>">
                    <option value="">Pilih Program Studi</option>

                    <?php 
                        if( $programStudi->num_rows > 0 ) :
                            while($row = $programStudi->fetch_object()) :
                    ?>
                    <option value="<?= $row->id; ?>"
                        <?= ( !is_null($mahasiswa) && $mahasiswa->program_studi_id == $row->id || old('prodi') == $row->id) ? 'selected' : ''; ?>>
                        <?= $row->nama; ?></option>
                    <?php endwhile; endif; ?>

                </select>
            </div>
            <div class="text-right">
                <button type="reset" class="btn btn-danger"><i class="bi bi-x-circle"></i> Reset</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            </div>

        </form>
    </div>
</div>

<?php 
    // hilangkan semua pesan error dan data lama.
    unset($_SESSION['errors']);
    unsetOld();

    // footer
    require_once 'layout/footer.php';
?>