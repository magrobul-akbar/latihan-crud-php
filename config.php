<?php

// session
session_start();

// koneksi
$con = mysqli_connect('localhost', 'root', '', 'crud2023');

// base url
if( !defined('BASEURL') ) {
    define('BASEURL', 'http://localhost/web/crud2023/');
}

// old data
function old($key, $value = false) {
    if( $value === false ) {
        if( isset($_SESSION['old'][$key]) ) {
            return $_SESSION['old'][$key];
        }

        return null;
    }

    $_SESSION['old'][$key] = $value;
}

function unsetOld($key = false)
{
    if( $key === false ) {
        if( isset($_SESSION['old']) ) {
            unset($_SESSION['old']);
        }
    } else {
        if( isset($_SESSION['old'][$key]) ) {
            unset( $_SESSION['old'][$key] );
        }
    }
    
}

// cek login
function is_login() {
    if( isset($_SESSION['login']) ) {
        return true;
    } else {
        return false;
    }
}

// ambil data user yang login
function user() {
    if( is_login() ) {
        global $con;
        $login = $_SESSION['login'];
        $user = mysqli_query($con, "SELECT * FROM users WHERE username = '$login' LIMIT 1");

        if( $user->num_rows > 0 ) {
            return $user->fetch_object();
        }

        return null;
    }

    return null;
}