<?php
// Central Global Settings Helper for Skill Grow Institution
if (!function_exists('getGlobalSettings')) {
    function getGlobalSettings() {
        static $cachedSettings = null;
        if ($cachedSettings !== null) {
            return $cachedSettings;
        }

        global $pdo;
        if (!isset($pdo)) {
            $dbPath = __DIR__ . '/db.php';
            if (file_exists($dbPath)) {
                require_once $dbPath;
            } elseif (file_exists(__DIR__ . '/../admin/config/db.php')) {
                require_once __DIR__ . '/../admin/config/db.php';
            }
        }

        $defaults = [
            'id' => 1,
            'site_title' => 'Skill Grow Institution',
            'site_logo' => 'assets/images/logo/logopng.png',
            'site_phone' => '+91 9217431562',
            'site_whatsapp' => '919217431562',
            'site_email' => 'info@skillgrowinstitution.in',
            'site_address' => 'Metro Plaza Meerut, 250002, India',
            'social_facebook' => 'https://facebook.com',
            'social_twitter' => 'https://twitter.com',
            'social_linkedin' => 'https://linkedin.com',
            'social_instagram' => 'https://instagram.com',
            'social_youtube' => '',
            'admission_fee' => 999.00,
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => '',
            'smtp_pass' => '',
            'smtp_encryption' => 'tls',
            'smtp_from_email' => 'admissions@skillgrowinstitution.in',
            'smtp_from_name' => 'Skill Grow Institution',
            'smtp_mode' => 'test',
            'razorpay_key_id' => '',
            'razorpay_key_secret' => '',
            'razorpay_mode' => 'test',
            'razorpay_status' => 'active'
        ];

        try {
            if (isset($pdo)) {
                $stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1 LIMIT 1");
                $row = $stmt->fetch();
                if ($row) {
                    $cachedSettings = array_merge($defaults, $row);
                    return $cachedSettings;
                }
            }
        } catch (Exception $e) {
            // fallback to defaults on error
        }

        $cachedSettings = $defaults;
        return $cachedSettings;
    }
}
