<?php
require_once __DIR__ . '/../config/init.php';
session_start();
session_unset();
session_destroy();
header('Location: ../index.php');
exit();