<?php
// Testimonials Helper - Auto-initializes and seeds testimonials table if needed

function ensureTestimonialsTable(PDO $pdo): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS testimonials (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            student_name VARCHAR(150) NOT NULL,
            student_age VARCHAR(20) DEFAULT '',
            student_designation VARCHAR(150) DEFAULT '',
            rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
            description TEXT NOT NULL,
            image_path VARCHAR(255) NOT NULL DEFAULT 'assets/images/testi/rahul-sharma.jpg',
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $count = (int)$pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
    if ($count === 0) {
        $defaults = [
            [
                'student_name'        => 'Rahul Sharma',
                'student_age'         => '22',
                'student_designation' => 'Pilot',
                'rating'              => 5,
                'description'         => "I always dreamed of flying and Skill Grow Institution helped turn that dream into reality. The trainers are industry experts and the mock interview sessions were a big help. Today, I'm proud to be selected by a leading airline.",
                'image_path'          => 'assets/images/testi/rahul-sharma.jpg',
                'status'              => 'active',
            ],
            [
                'student_name'        => 'Priya Mehta',
                'student_age'         => '20',
                'student_designation' => 'Hospitality Management Student',
                'rating'              => 5,
                'description'         => "After joining the Hospitality course, I’ve gained both knowledge and confidence. Skill Grow Institution gave me practical exposure through hotel visits and guest lectures. I recently got placed at a 5-star property in Mumbai. I'm truly thankful!",
                'image_path'          => 'assets/images/testi/priya.jpg',
                'status'              => 'active',
            ],
            [
                'student_name'        => 'Aman Verma',
                'student_age'         => '24',
                'student_designation' => 'Tourism & Travel Graduate',
                'rating'              => 5,
                'description'         => "Skill Grow Institution prepared me for the travel industry in the best possible way. I learned how to manage travel bookings, client handling, and even international tourism trends. I’m now working with a reputed travel agency in Delhi.",
                'image_path'          => 'assets/images/testi/aman.jpg',
                'status'              => 'active',
            ],
            [
                'student_name'        => 'Sneha Rajput',
                'student_age'         => '21',
                'student_designation' => 'Ground Staff Executive',
                'rating'              => 5,
                'description'         => "From grooming sessions to communication skills, everything was top-notch at Skill Grow Institution. I always wanted to work at the airport and now I'm working at IGI Airport, New Delhi. This institute changed my life.",
                'image_path'          => 'assets/images/testi/sneha.jpeg',
                'status'              => 'active',
            ],
            [
                'student_name'        => 'Neha Kapoor',
                'student_age'         => '23',
                'student_designation' => 'Digital Marketing Specialist',
                'rating'              => 5,
                'description'         => "The Digital Marketing & E-Commerce program at Skill Grow Institution gave me hands-on experience with live ad campaigns and SEO analytics. Within a month of completing my course, I secured a great role at a top digital agency.",
                'image_path'          => 'assets/images/testi/neha-kapoor.webp',
                'status'              => 'active',
            ],
            [
                'student_name'        => 'Amin Khan',
                'student_age'         => '22',
                'student_designation' => 'Full-Stack Web Developer',
                'rating'              => 5,
                'description'         => "Learning Web Technology and Python through practical projects at Skill Grow Institution transformed my coding confidence. The mentors guided me step-by-step from fundamentals to building real-world applications.",
                'image_path'          => 'assets/images/testi/amin.jpg',
                'status'              => 'active',
            ],
            [
                'student_name'        => 'Menka Sharma',
                'student_age'         => '21',
                'student_designation' => 'UI/UX & Creative Designer',
                'rating'              => 5,
                'description'         => "The Design & UX curriculum at Skill Grow Institution is completely aligned with modern industry standards. Building my portfolio under expert mentorship helped me land my dream design role effortlessly.",
                'image_path'          => 'assets/images/testi/Menka-Sharma-Test.jpg.webp',
                'status'              => 'active',
            ],
            [
                'student_name'        => 'Riya Tiwari',
                'student_age'         => '23',
                'student_designation' => 'Business Analyst',
                'rating'              => 5,
                'description'         => "Skill Grow Institution's Business Studies and Data Analytics training helped me bridge the gap between academic theory and corporate decision-making. Highly recommended for ambitious students!",
                'image_path'          => 'assets/images/testi/priya.jpeg',
                'status'              => 'active',
            ],
        ];

        $ins = $pdo->prepare("
            INSERT INTO testimonials (student_name, student_age, student_designation, rating, description, image_path, status)
            VALUES (:name, :age, :desig, :rating, :desc, :img, :status)
        ");
        foreach ($defaults as $row) {
            $ins->execute([
                'name'   => $row['student_name'],
                'age'    => $row['student_age'],
                'desig'  => $row['student_designation'],
                'rating' => $row['rating'],
                'desc'   => $row['description'],
                'img'    => $row['image_path'],
                'status' => $row['status'],
            ]);
        }
    }
}

function formatTestimonialHeadline(array $t): string {
    $parts = [];
    if (!empty(trim($t['student_name'] ?? ''))) {
        $parts[] = trim($t['student_name']);
    }
    if (!empty(trim($t['student_age'] ?? ''))) {
        $parts[] = trim($t['student_age']);
    }
    if (!empty(trim($t['student_designation'] ?? ''))) {
        $parts[] = trim($t['student_designation']);
    }
    return implode(', ', $parts);
}
