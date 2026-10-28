<?php
// Student Logout
session_start();
unset($_SESSION['student_logged_in']);
unset($_SESSION['student_id']);
unset($_SESSION['student_name']);
unset($_SESSION['student_email']);
unset($_SESSION['student_phone']);
unset($_SESSION['student_course']);
unset($_SESSION['student_app_id']);

header("Location: index.php");
exit;
