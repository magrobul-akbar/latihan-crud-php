<?php 

// config
require_once 'config.php';

// kika user sudah login arahkan kembali kehalaman index.
if( is_login() ) {
    header("Location: index.php");
    return;
}

// jika tombol login diklik.
if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $loginId = htmlspecialchars($_POST['loginId']);
    $password = htmlspecialchars($_POST['password']);

    // simpan data lama
    old('loginId', $loginId);

    // validasi field
    $errors = [];
    if( empty($loginId) ) {
        $errors['loginId'] = 'Masukan Email atau Username!';
    }

    if( empty($password) ) {
        $errors['password'] = 'Masukan password!';
    }

    // kirimkan pesan error jika ada.
    if( count($errors) > 0 ) {
        $_SESSION['errors'] = $errors;
        header("Location: login.php");
        return;
    }

    // cek akun
    $user = mysqli_query($con, "SELECT username, password
                                FROM users
                                WHERE username = '$loginId'
                                OR email = '$loginId' LIMIT 1"
                        );
    
    // jika user tidak ditemukan
    if( $user->num_rows === 0 ) {
        $_SESSION['errors']['loginId'] = 'Akun tidak ditemukan!';
        header("Location: login.php");
        return;
    }

    // ambil data user.
    $user = $user->fetch_object();

    // jika password salah
    if( !password_verify($password, $user->password) ) {
        $_SESSION['errors']['password'] = 'Password salah!';
        header("Location: login.php");
        return;
    }

    // hilangkan semua pesan error dan data lama.
    unset($_SESSION['errors']);
    unsetOld();

    $_SESSION['login'] = $user->username;
    $_SESSION['success'] = 'Selamat datang ' . $user->username . ' di CRUD' . date('Y');
    header("Location: index.php");
    return;
}

// header
$title = 'Login';
require_once 'layout/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-4 col-lg-5 p-0">
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

        <!-- pessan success -->
        <?php if( isset($_SESSION['success']) ) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <div class="card bg-white">
            <div class="card-header border-bottom bg-white">
                <h5 class="card-title text-center m-0 p-0">CRUD<?= date('Y'); ?></h5>
            </div>
            <div class="card-body">
                <form action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="loginId">Email atau Username</label>
                        <input type="text" name="loginId"
                            class="form-control <?= (isset($_SESSION['errors']['loginId'])) ? 'is-invalid' : ''; ?>"
                            value="<?= old('loginId'); ?>" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password"
                            class="form-control <?= (isset($_SESSION['errors']['password'])) ? 'is-invalid' : ''; ?>">
                    </div>
                    <button class="w-100 btn btn-primary" type="submit"><i class="bi bi-door-open"></i> Masuk</button>
                </form>

            </div>
        </div>
    </div>
</div>


<?php 
    // hilangkan semua pesan error dan data lama.
    unset($_SESSION['errors']);
    unsetOld();

    // footer
    require_once 'layout/footer.php';
?>