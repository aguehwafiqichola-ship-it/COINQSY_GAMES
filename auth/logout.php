<?php
session_start();
include '../bd/bd.php';

session_destroy();
header("Location: login.php");
exit;
