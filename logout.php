<?php
// LOGOUT (Modul 7): hapus semua data session lalu kembali ke login.
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
