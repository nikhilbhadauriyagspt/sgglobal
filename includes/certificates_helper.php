<?php
// Central Certificate Helper for Skill Grow Institution
require_once __DIR__ . '/mail_helper.php';

function ensureCertificatesTable(PDO $pdo): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificates (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            certificate_number VARCHAR(80) NOT NULL UNIQUE,
            student_id VARCHAR(50) DEFAULT NULL,
            student_name VARCHAR(150) NOT NULL,
            student_email VARCHAR(150) NOT NULL,
            course_name VARCHAR(200) NOT NULL,
            enrollment_id VARCHAR(80) DEFAULT NULL,
            certificate_type VARCHAR(100) DEFAULT 'Certificate of Completion',
            grade VARCHAR(20) DEFAULT 'A (Distinction)',
            issue_date DATE NOT NULL,
            status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
            admin_notes TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_cert_student (student_id),
            INDEX idx_cert_email (student_email),
            INDEX idx_cert_number (certificate_number)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    ensureCertificateSettingsColumns($pdo);
}

function ensureCertificateSettingsColumns(PDO $pdo): void {
    static $colsChecked = false;
    if ($colsChecked) return;
    $colsChecked = true;

    $newCols = [
        'cert_sign1_name'    => "VARCHAR(150) DEFAULT 'Dr. R. K. Singhania'",
        'cert_sign1_title'   => "VARCHAR(150) DEFAULT 'Academic Director'",
        'cert_sign1_sub'     => "VARCHAR(150) DEFAULT 'Skill Grow Institution'",
        'cert_sign1_image'   => "VARCHAR(255) DEFAULT ''",
        'cert_sign2_name'    => "VARCHAR(150) DEFAULT 'Priya Sharma'",
        'cert_sign2_title'   => "VARCHAR(150) DEFAULT 'Controller of Examinations'",
        'cert_sign2_sub'     => "VARCHAR(150) DEFAULT 'Academic Council'",
        'cert_sign2_image'   => "VARCHAR(255) DEFAULT ''",
        'cert_subtitle'      => "VARCHAR(255) DEFAULT 'National Academy of Professional & Industry Excellence'",
        'cert_seal_text'     => "VARCHAR(100) DEFAULT 'OFFICIAL SEAL'",
        'cert_verified_text' => "VARCHAR(100) DEFAULT 'VERIFIED CREDENTIAL'"
    ];

    try {
        $existingCols = [];
        $stmt = $pdo->query("DESCRIBE system_settings");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
            $existingCols[] = $col['Field'];
        }

        foreach ($newCols as $colName => $def) {
            if (!in_array($colName, $existingCols, true)) {
                $pdo->exec("ALTER TABLE system_settings ADD COLUMN `{$colName}` {$def}");
            }
        }
    } catch (\Exception $e) {
        // Table or columns already up to date
    }
}

function getCertificateSettings(PDO $pdo): array {
    ensureCertificateSettingsColumns($pdo);
    
    $defaults = [
        'cert_sign1_name'    => 'Dr. R. K. Singhania',
        'cert_sign1_title'   => 'Academic Director',
        'cert_sign1_sub'     => 'Skill Grow Institution',
        'cert_sign1_image'   => '',
        'cert_sign2_name'    => 'Priya Sharma',
        'cert_sign2_title'   => 'Controller of Examinations',
        'cert_sign2_sub'     => 'Academic Council',
        'cert_sign2_image'   => '',
        'cert_subtitle'      => 'National Academy of Professional & Industry Excellence',
        'cert_seal_text'     => 'OFFICIAL SEAL',
        'cert_verified_text' => 'VERIFIED CREDENTIAL'
    ];

    try {
        $stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1 LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            foreach ($defaults as $k => $v) {
                if (!empty($row[$k])) {
                    $defaults[$k] = $row[$k];
                }
            }
        }
    } catch (\Exception $e) {}

    return $defaults;
}

function generateCertificateNumber(PDO $pdo): string {
    $year = date('Y');
    for ($i = 0; $i < 10; $i++) {
        $rand = mt_rand(10000, 99999);
        $num = "SGI-CERT-{$year}-{$rand}";
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates WHERE certificate_number = :num");
        $stmt->execute(['num' => $num]);
        if ((int)$stmt->fetchColumn() === 0) {
            return $num;
        }
    }
    return "SGI-CERT-{$year}-" . time();
}

function sendCertificateApprovalEmail(PDO $pdo, array $cert): array {
    $toEmail = $cert['student_email'];
    $toName  = $cert['student_name'];
    $certNum = $cert['certificate_number'];
    $course  = $cert['course_name'];
    $issueDt = date('d M, Y', strtotime($cert['issue_date']));
    $type    = $cert['certificate_type'] ?? 'Certificate of Completion';
    $grade   = $cert['grade'] ?? 'A (Distinction)';

    $settings = getGlobalSettings();
    $siteName = $settings['site_title'] ?: 'Skill Grow Institution';

    $serverHost = $_SERVER['HTTP_HOST'] ?? 'localhost:8009';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $viewUrl = "{$protocol}://{$serverHost}/view-certificate.php?cert=" . urlencode($certNum);
    $portalUrl = "{$protocol}://{$serverHost}/my-enrollments.php";

    $subject = "Official Certificate Issued: {$course} — {$certNum}";

    $htmlBody = "
    <div style='background-color:#070e24; padding:35px 20px; font-family:\"Helvetica Neue\",Helvetica,Arial,sans-serif;'>
        <div style='max-width:620px; margin:0 auto; background-color:#0f1537; border:1px solid #1e2c52; border-top:4px solid #e6a910; border-radius:14px; overflow:hidden;'>
            <div style='padding:28px 32px 18px; text-align:center; background-color:#000c27;'>
                <h1 style='color:#ffffff; font-size:22px; margin:0 0 6px; font-family:Georgia,serif;'>{$siteName}</h1>
                <p style='color:#e6a910; font-size:12px; font-weight:bold; letter-spacing:1px; margin:0; text-transform:uppercase;'>Official Certification Board</p>
            </div>
            <div style='padding:32px 32px 24px; color:#e2e8f0; line-height:1.6;'>
                <p style='font-size:15px; margin-top:0;'>Dear <strong>" . htmlspecialchars($toName) . "</strong>,</p>
                <p style='font-size:14px; color:#cbd5e1;'>
                    Congratulations! Your academic evaluation for <strong>" . htmlspecialchars($course) . "</strong> has been verified and approved by the academic council.
                </p>
                <div style='background-color:#070e24; border:1px solid #1e2c52; border-radius:10px; padding:18px 20px; margin:22px 0;'>
                    <table style='width:100%; border-collapse:collapse; font-size:13px; color:#cbd5e1;'>
                        <tr>
                            <td style='padding:6px 0; color:#94a3b8;'>Certificate ID:</td>
                            <td style='padding:6px 0; font-family:monospace; color:#e6a910; font-weight:bold; text-align:right;'>{$certNum}</td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0; color:#94a3b8;'>Program / Course:</td>
                            <td style='padding:6px 0; color:#ffffff; font-weight:bold; text-align:right;'>" . htmlspecialchars($course) . "</td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0; color:#94a3b8;'>Certification Type:</td>
                            <td style='padding:6px 0; color:#ffffff; text-align:right;'>" . htmlspecialchars($type) . "</td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0; color:#94a3b8;'>Performance Grade:</td>
                            <td style='padding:6px 0; color:#10b981; font-weight:bold; text-align:right;'>" . htmlspecialchars($grade) . "</td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0; color:#94a3b8;'>Date of Issue:</td>
                            <td style='padding:6px 0; color:#ffffff; text-align:right;'>{$issueDt}</td>
                        </tr>
                    </table>
                </div>
                <div style='text-align:center; margin:30px 0 20px;'>
                    <a href='{$viewUrl}' target='_blank' style='background:linear-gradient(135deg, #f59e0b 0%, #e6a910 100%); color:#000c27; text-decoration:none; font-weight:bold; font-size:14px; padding:12px 28px; border-radius:8px; display:inline-block; box-shadow:0 4px 12px rgba(230,169,16,0.3);'>
                        View &amp; Print Official Certificate
                    </a>
                </div>
                <p style='font-size:12px; color:#94a3b8; text-align:center; margin-bottom:0;'>
                    You can also access your certificate anytime from your <a href='{$portalUrl}' style='color:#e6a910; text-decoration:none;'>Student Dashboard</a> under the <strong>Certificates</strong> tab.
                </p>
            </div>
            <div style='background-color:#000c27; padding:16px 20px; text-align:center; font-size:11px; color:#64748b; border-top:1px solid #1e2c52;'>
                &copy; " . date('Y') . " {$siteName}. All rights reserved. Registered Institution &amp; Verification Board.
            </div>
        </div>
    </div>";

    return sendSgiEmail($pdo, $toEmail, $toName, $subject, $htmlBody);
}
