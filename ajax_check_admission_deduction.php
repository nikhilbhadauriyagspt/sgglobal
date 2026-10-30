<?php
// Endpoint to check if a student has an existing paid admission/registration for a specific course
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/settings_helper.php';

$email = trim($_GET['email'] ?? ($_POST['email'] ?? ''));
$courseSlug = trim($_GET['course_slug'] ?? ($_POST['course_slug'] ?? ''));
$courseId   = (int)($_GET['course_id'] ?? ($_POST['course_id'] ?? 0));

if (empty($email) && !empty($_SESSION['student_email'])) {
    $email = $_SESSION['student_email'];
}

if (empty($email)) {
    echo json_encode([
        'has_admission' => false,
        'deduction' => 0.00,
        'message' => 'No student email provided'
    ]);
    exit;
}

try {
    // 1. Get Course Title
    $courseTitle = '';
    if ($courseId > 0) {
        $cStmt = $pdo->prepare("SELECT title, slug FROM courses WHERE id = :id LIMIT 1");
        $cStmt->execute(['id' => $courseId]);
        $c = $cStmt->fetch();
        if ($c) {
            $courseTitle = $c['title'];
            $courseSlug = $c['slug'];
        }
    } elseif (!empty($courseSlug)) {
        $cStmt = $pdo->prepare("SELECT title, slug FROM courses WHERE slug = :slug LIMIT 1");
        $cStmt->execute(['slug' => $courseSlug]);
        $c = $cStmt->fetch();
        if ($c) {
            $courseTitle = $c['title'];
        }
    }

    // 2. Query admissions table for a paid admission matching student email and course
    // Also ensure this admission hasn't already been fully deducted/consumed in course_enrollments
    $admStmt = $pdo->prepare("
        SELECT a.id, a.application_id, a.student_id, a.full_name, a.course_name, a.fee_amount, a.created_at
        FROM admissions a
        WHERE (a.email = :em OR a.student_id = :sid)
          AND a.payment_status = 'paid'
          AND (
            LOWER(TRIM(a.course_name)) = LOWER(TRIM(:cname))
            OR LOWER(REPLACE(a.course_name, ' ', '-')) LIKE LOWER(CONCAT('%', :cslug, '%'))
          )
          AND a.application_id NOT IN (
            SELECT COALESCE(admission_id_linked, '') FROM course_enrollments WHERE payment_status = 'paid' AND admission_id_linked IS NOT NULL
          )
        ORDER BY a.id DESC
        LIMIT 1
    ");

    $sid = $_SESSION['student_id'] ?? '';
    $admStmt->execute([
        'em'    => $email,
        'sid'   => $sid,
        'cname' => $courseTitle,
        'cslug' => $courseSlug
    ]);

    $adm = $admStmt->fetch();

    if ($adm) {
        echo json_encode([
            'has_admission'      => true,
            'application_id'     => $adm['application_id'],
            'student_id'         => $adm['student_id'],
            'course_name'        => $adm['course_name'],
            'deduction_amount'   => (float)$adm['fee_amount'],
            'formatted_deduction'=> '₹' . number_format((float)$adm['fee_amount'], 0),
            'registered_date'    => date('d M, Y', strtotime($adm['created_at'])),
            'message'            => 'Eligible for ₹' . number_format((float)$adm['fee_amount'], 0) . ' registration fee deduction'
        ]);
    } else {
        echo json_encode([
            'has_admission'      => false,
            'deduction_amount'   => 0.00,
            'formatted_deduction'=> '₹0',
            'message'            => 'No prior registration found for this course.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'has_admission' => false,
        'deduction_amount' => 0.00,
        'error' => $e->getMessage()
    ]);
}
