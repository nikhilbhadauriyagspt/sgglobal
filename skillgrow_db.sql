-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: skillgrow_db
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Administrator',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'super_admin',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Super Admin','admin','admin@skillgrowinstitution.in','$2y$10$9tQjJf4wDGveE/jsNbIUBOsKO8Ce1QY6VWmfdar.Cli38sfp4kQDS','super_admin',NULL,'active','2026-10-28 23:32:49','2026-10-28 17:53:40','2026-10-28 18:02:49');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admissions`
--

DROP TABLE IF EXISTS `admissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `application_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qualification` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhaar_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fee_amount` decimal(10,2) DEFAULT '999.00',
  `payment_status` enum('paid','pending','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'paid',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'UPI',
  `admission_status` enum('verified','under_review','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'verified',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `photo_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhaar_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marksheet_doc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_order_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_verified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_id` (`application_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admissions`
--

LOCK TABLES `admissions` WRITE;
/*!40000 ALTER TABLE `admissions` DISABLE KEYS */;
INSERT INTO `admissions` VALUES (1,NULL,'SGI-2026-88412','Aarav Sharma','aarav.sharma@gmail.com','9876543210','Full Stack Web & Cloud Development','B.Tech CS','XXXX-XXXX-4512',999.00,'paid','UPI / GPay','verified','2026-10-28 15:53:53',NULL,NULL,NULL,NULL,NULL,NULL,0),(2,NULL,'SGI-2026-88413','Priya Verma','priya.verma@yahoo.com','9811223344','Airport Operations & Ground Handling','Graduate (BA)','XXXX-XXXX-8921',999.00,'paid','Debit Card','approved','2026-10-28 12:53:53',NULL,NULL,NULL,NULL,NULL,NULL,0),(3,NULL,'SGI-2026-88414','Rohan Mehta','rohan.mehta@outlook.com','9988776655','Digital Marketing & Growth E-Commerce','BBA','XXXX-XXXX-3341',999.00,'paid','UPI / PhonePe','under_review','2026-10-27 17:53:53',NULL,NULL,NULL,NULL,NULL,NULL,0),(4,NULL,'SGI-2026-88415','Simran Kaur','simran.k@gmail.com','9765432109','Banking, Wealth & Financial Analytics','B.Com','XXXX-XXXX-7120',999.00,'paid','Net Banking','verified','2026-10-26 17:53:53',NULL,NULL,NULL,NULL,NULL,NULL,0),(5,NULL,'SGI-2026-88416','Kunal Deshmukh','kunal.d@gmail.com','9654321876','Hospitality & Luxury Tourism Management','12th Pass','XXXX-XXXX-9011',999.00,'paid','UPI / Paytm','approved','2026-10-25 17:53:53',NULL,NULL,NULL,NULL,NULL,NULL,0),(6,'STU-2026-8174','SGI-2026-36972','Mohit Verma','mohit.v@example.com','9898989898','Full Stack Web Technology','BCA','1234-5678-9012',999.00,'paid','Razorpay','verified','2026-10-28 18:31:56',NULL,NULL,NULL,'order_test_112233','pay_test_998877',NULL,1),(7,'STU-2026-3406','SGI-2026-28820','Vikram Rajput','vikram.r@example.com','9812345678','Full Stack Web Engineering','B.Tech','9988-7766-5544',999.00,'paid','Razorpay','verified','2026-10-28 18:47:07',NULL,NULL,NULL,'order_vikram_101','pay_test_vikram_101',NULL,1),(8,'STU-2026-1610','SGI-2026-96952','nikhil','abcd@gmail.com','06676767677','Advanced Management Studies','Bachelor\'s Degree','5345 - 2345 - 3425',999.00,'paid','Razorpay (Test Simulation)','verified','2026-10-28 19:02:31','uploads/students/nikhil_SGI-2026-96952/student_photo.png','uploads/students/nikhil_SGI-2026-96952/aadhaar_card.png','uploads/students/nikhil_SGI-2026-96952/academic_marksheet.png','ORD-1793214151019','pay_sim_11546245',NULL,1);
/*!40000 ALTER TABLE `admissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_categories`
--

DROP TABLE IF EXISTS `course_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'fa-graduation-cap',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_categories`
--

LOCK TABLES `course_categories` WRITE;
/*!40000 ALTER TABLE `course_categories` DISABLE KEYS */;
INSERT INTO `course_categories` VALUES (1,'Technology & IT','technology-it','fa-laptop-code','active','2026-10-28 18:37:07'),(2,'Business & Management','business-management','fa-briefcase','active','2026-10-28 18:37:07'),(3,'Aviation & Hospitality','aviation-hospitality','fa-plane-departure','active','2026-10-28 18:37:07'),(4,'Marketing & E-Commerce','marketing-ecommerce','fa-bullhorn','active','2026-10-28 18:37:07'),(5,'Design & Creative Arts','design-creative','fa-palette','active','2026-10-28 18:37:07'),(6,'Finance & Investment','finance-investment','fa-chart-line','active','2026-10-28 18:37:07');
/*!40000 ALTER TABLE `course_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_enrollments`
--

DROP TABLE IF EXISTS `course_enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `enrollment_id` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `course_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_slug` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('paid','pending','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'paid',
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Razorpay',
  `razorpay_payment_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_order_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollment_id` (`enrollment_id`),
  KEY `student_id` (`student_id`),
  KEY `course_slug` (`course_slug`),
  KEY `enrollment_id_2` (`enrollment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_enrollments`
--

LOCK TABLES `course_enrollments` WRITE;
/*!40000 ALTER TABLE `course_enrollments` DISABLE KEYS */;
INSERT INTO `course_enrollments` VALUES (1,'ENR-2026-WEBT-7817','STU-2026-8174',4,'Full Stack Web Engineering','web-technology','Mohit Verma','mohit.v@example.com','9876543210',18999.00,'paid','Razorpay','pay_test_127116','ord_test_1793214665','active','2026-10-28 19:11:05');
/*!40000 ALTER TABLE `course_enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Certificate Program',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/images/course/web-technology.webp',
  `rating` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '4.9',
  `reviews` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '350+',
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '6 Months',
  `effort` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '10 hrs / week',
  `mode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Live & Hands-on',
  `fee` decimal(10,2) DEFAULT '14999.00',
  `eligibility` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT '10+2 / Any Graduate',
  `language` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'English & Hindi',
  `avg_salary` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '?4.5L - ?9.5L / yr',
  `placement_rate` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '95% Placed',
  `overview` longtext COLLATE utf8mb4_unicode_ci,
  `stats` json DEFAULT NULL,
  `pillars` json DEFAULT NULL,
  `curriculum` json DEFAULT NULL,
  `careers` json DEFAULT NULL,
  `tools` json DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_course_category` FOREIGN KEY (`category_id`) REFERENCES `course_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,2,'Business Studies & Enterprise Management','business-studies','Master corporate operations, financial acumen, and high-growth business frameworks.','Executive Diploma','assets/images/course/business-intelligence-png.png','4.8','320+','6 Months','8-10 hrs / week','Hybrid (Live + Campus)',16999.00,'10+2 in Any Stream','English & Hindi','₹4.2L - ₹8.5L / yr','94% Placed','Step into the modern business arena with an enterprise-ready skillset. This program takes you beyond textbooks into the realities of corporate finance, strategic planning, team leadership, and digital operations.','[{\"val\": \"10+\", \"label\": \"Live Case Studies\"}, {\"val\": \"94%\", \"label\": \"Placement Success\"}, {\"val\": \"1:1\", \"label\": \"Executive Mentoring\"}, {\"val\": \"100%\", \"label\": \"Practical Drills\"}]','[{\"desc\": \"Balance sheet auditing, budgeting funnels, and cash-flow health monitoring.\", \"icon\": \"fa-chart-pie\", \"title\": \"Financial Literacy\"}, {\"desc\": \"Cross-functional leadership, organizational behavior, and KPI systems.\", \"icon\": \"fa-sitemap\", \"title\": \"Corporate Structure\"}, {\"desc\": \"Go-to-market strategies, customer segmentation, and product-market fit.\", \"icon\": \"fa-bullseye\", \"title\": \"Market Positioning\"}, {\"desc\": \"Commercial agreements, compliance regulations, and intellectual property.\", \"icon\": \"fa-scale-balanced\", \"title\": \"Corporate Law & Ethics\"}]','[{\"num\": \"01\", \"title\": \"Enterprise Architecture & Operations\", \"topics\": [\"Modern corporate hierarchies\", \"Workflow automation\", \"Operational planning\", \"Supply coordination\"], \"duration\": \"4 Weeks\"}, {\"num\": \"02\", \"title\": \"Managerial Finance & Accounting\", \"topics\": [\"P&L statements\", \"Cash flow analysis\", \"Budget forecasting\", \"Venture capital basics\"], \"duration\": \"5 Weeks\"}, {\"num\": \"03\", \"title\": \"Growth Marketing & Consumer Psychology\", \"topics\": [\"Audience segmentation\", \"Brand equity\", \"Sales funnels\", \"Omnichannel strategy\"], \"duration\": \"5 Weeks\"}, {\"num\": \"04\", \"title\": \"Commercial Law, Ethics & Contracts\", \"topics\": [\"Statutory compliances\", \"Employment law\", \"Vendor SLAs\", \"IP & Trademark basics\"], \"duration\": \"4 Weeks\"}, {\"num\": \"05\", \"title\": \"Capstone: Real-World Business Launch Plan\", \"topics\": [\"Pitch deck creation\", \"Unit economics modeling\", \"Live boardroom presentation\"], \"duration\": \"6 Weeks\"}]','[{\"role\": \"Business Development Lead\", \"demand\": \"High\", \"package\": \"₹5.5 - 9 LPA\"}, {\"role\": \"Operations Analyst\", \"demand\": \"Very High\", \"package\": \"₹4.5 - 8 LPA\"}, {\"role\": \"Associate Project Manager\", \"demand\": \"High\", \"package\": \"₹6.0 - 10 LPA\"}, {\"role\": \"Corporate Strategy Trainee\", \"demand\": \"Surging\", \"package\": \"₹5.0 - 8.5 LPA\"}]','[\"MS Excel Advanced\", \"Power BI Basics\", \"Trello / Jira\", \"ERP Overview\", \"Notion for Teams\"]','active','2026-10-28 18:37:07','2026-10-28 18:54:57'),(2,2,'Advanced Management Studies','management-studies','Transform from an individual contributor into a decisive, high-impact organizational leader.','Leadership Cohort','assets/images/course/management.webp','4.9','450+','1 Year','10 hrs / week','Executive Classroom & Live Labs',21999.00,'10+2 / Any Graduate','English & Hindi','₹5.0L - ₹11L / yr','96% Placed','Designed for aspiring team leads and project managers. Develop strategic decision-making instincts, organizational leadership capabilities, agile product methodologies, and crisis-resolution acumen.','[{\"val\": \"12+\", \"label\": \"Strategy Simulations\"}, {\"val\": \"96%\", \"label\": \"Career Transitions\"}, {\"val\": \"50+\", \"label\": \"Hiring Partners\"}, {\"val\": \"Dual\", \"label\": \"Certification\"}]','[{\"desc\": \"SWOT matrix, competitive moat building, and long-term business roadmap design.\", \"icon\": \"fa-chess\", \"title\": \"Strategic Thinking\"}, {\"desc\": \"Scrum ceremonies, team motivation psychology, and performance governance.\", \"icon\": \"fa-users-gear\", \"title\": \"Team Leadership & Agile\"}, {\"desc\": \"Lean manufacturing principles, vendor SLAs, and supply-chain risk mitigation.\", \"icon\": \"fa-truck-fast\", \"title\": \"Operations & Supply Resiliency\"}, {\"desc\": \"Recruitment pipelines, appraisal methodologies, and conflict negotiation.\", \"icon\": \"fa-handshake\", \"title\": \"Talent & Human Capital\"}]','[{\"num\": \"01\", \"title\": \"Executive Mindset & People Management\", \"topics\": [\"Leadership archetypes\", \"Conflict de-escalation\", \"Performance coaching\", \"Diversity management\"], \"duration\": \"6 Weeks\"}, {\"num\": \"02\", \"title\": \"Strategic Analysis & Business Models\", \"topics\": [\"Porter’s 5 Forces\", \"Platform business models\", \"Blue ocean strategy\", \"Market disruption\"], \"duration\": \"6 Weeks\"}, {\"num\": \"03\", \"title\": \"Agile Frameworks & Project Delivery\", \"topics\": [\"Scrum master fundamentals\", \"Kanban flow\", \"Sprint planning\", \"Jira enterprise setup\"], \"duration\": \"6 Weeks\"}, {\"num\": \"04\", \"title\": \"Supply Chain, Quality & Six Sigma Basics\", \"topics\": [\"Lean principles\", \"Inventory management\", \"Procurement negotiation\", \"Root cause analysis\"], \"duration\": \"6 Weeks\"}, {\"num\": \"05\", \"title\": \"Executive Boardroom Crisis Simulation\", \"topics\": [\"Live crisis management\", \"Public relations response\", \"Executive presentation\"], \"duration\": \"6 Weeks\"}]','[{\"role\": \"Operations Manager\", \"demand\": \"Very High\", \"package\": \"₹6.5 - 12 LPA\"}, {\"role\": \"Scrum Master / Agile Lead\", \"demand\": \"Surging\", \"package\": \"₹7.0 - 14 LPA\"}, {\"role\": \"Project Coordinator\", \"demand\": \"High\", \"package\": \"₹5.5 - 9 LPA\"}, {\"role\": \"HR Business Partner Trainee\", \"demand\": \"Moderate\", \"package\": \"₹4.8 - 8 LPA\"}]','[\"Jira Software\", \"Confluence\", \"Asana\", \"Tableau Desktop\", \"Miro Boards\"]','active','2026-10-28 18:37:07','2026-10-28 18:54:57'),(3,6,'Financial Markets, Banking & Wealth','banking-and-finance','Navigate stock markets, equity valuation, credit risk, and modern retail banking ecosystems.','Banking Fast-Track','assets/images/course/finacial.png','4.9','510+','6 Months','10 hrs / week','Live Market Lab & Classroom',19999.00,'10+2 / Commerce / Any Graduate','English & Hindi','₹4.5L - ₹9.5L / yr','93% Placed','From equity market trading mechanisms to commercial banking underwriting. Master the instruments shaping modern financial architecture—stocks, derivatives, bonds, mutual funds, and digital fintech rails.','[{\"val\": \"100%\", \"label\": \"Live Market Labs\"}, {\"val\": \"40+\", \"label\": \"Financial Models\"}, {\"val\": \"93%\", \"label\": \"Placement Rate\"}, {\"val\": \"RBI\", \"label\": \"Aligned Guidelines\"}]','[{\"desc\": \"Core banking systems, credit risk assessment, and retail loan underwriting.\", \"icon\": \"fa-building-columns\", \"title\": \"Commercial Banking\"}, {\"desc\": \"Technical & fundamental analysis, NSE/BSE mechanisms, and IPO valuations.\", \"icon\": \"fa-chart-line\", \"title\": \"Equity & Capital Markets\"}, {\"desc\": \"Mutual funds, asset allocation, tax-saving instruments, and retirement funds.\", \"icon\": \"fa-coins\", \"title\": \"Wealth & Portfolio Design\"}, {\"desc\": \"UPI, merchant payment gateways, neo-banking, and digital lending algorithms.\", \"icon\": \"fa-credit-card\", \"title\": \"Fintech & Digital Rails\"}]','[{\"num\": \"01\", \"title\": \"Commercial Banking & Regulatory Framework\", \"topics\": [\"Banking regulation act\", \"KYC/AML protocols\", \"Lending instruments\", \"NPA management\"], \"duration\": \"4 Weeks\"}, {\"num\": \"02\", \"title\": \"Stock Exchanges & Technical Analysis\", \"topics\": [\"Candlestick patterns\", \"Chart indicators\", \"Derivatives (F&O)\", \"Trading psychology\"], \"duration\": \"5 Weeks\"}, {\"num\": \"03\", \"title\": \"Financial Statement Analysis & Modeling\", \"topics\": [\"Ratio analysis\", \"Cash flow health\", \"DCF valuation models\", \"Credit score (CIBIL) evaluation\"], \"duration\": \"5 Weeks\"}, {\"num\": \"04\", \"title\": \"Mutual Funds, Insurance & Wealth Planning\", \"topics\": [\"Equity vs Debt funds\", \"Asset allocation\", \"Insurance underwriting\", \"Client advisory\"], \"duration\": \"4 Weeks\"}, {\"num\": \"05\", \"title\": \"Fintech Innovations & Capstone Case Study\", \"topics\": [\"Payment gateway integrations\", \"Digital KYC\", \"Comprehensive wealth plan design\"], \"duration\": \"4 Weeks\"}]','[{\"role\": \"Banking Relationship Manager\", \"demand\": \"Very High\", \"package\": \"₹4.8 - 8.5 LPA\"}, {\"role\": \"Equity Research Associate\", \"demand\": \"High\", \"package\": \"₹5.5 - 11 LPA\"}, {\"role\": \"Credit & Loan Underwriter\", \"demand\": \"High\", \"package\": \"₹4.5 - 7.5 LPA\"}, {\"role\": \"Wealth Advisory Specialist\", \"demand\": \"Surging\", \"package\": \"₹5.0 - 10 LPA\"}]','[\"Finacle Overview\", \"Excel Financial Modeling\", \"TradingView\", \"NSE Terminal\", \"Bloomberg Basics\"]','active','2026-10-28 18:37:07','2026-10-28 18:54:57'),(4,1,'Full Stack Web Engineering','web-technology','Architect responsive digital products with HTML5, CSS3, Modern JavaScript, PHP & MySQL.','Flagship Program','assets/images/course/web-technology.webp','4.9','850+','6 Months','12 hrs / week','Interactive Coding Labs',18999.00,'10+2 / BCA / B.Tech / Any Graduate','English & Hindi','₹4.8L - ₹12L / yr','97% Placed','Transform from code enthusiast to production-ready software engineer. Build lightning-fast user interfaces, architect resilient server-side APIs, design secure databases, and deploy live applications to modern cloud servers.','[{\"val\": \"5+\", \"label\": \"Live Fullstack Apps\"}, {\"val\": \"97%\", \"label\": \"Placement Ratio\"}, {\"val\": \"100+\", \"label\": \"Coding Drills\"}, {\"val\": \"Git\", \"label\": \"Portfolio Built\"}]','[{\"desc\": \"HTML5, modern CSS3 Flexbox/Grid, Bootstrap 5, responsive layouts and animations.\", \"icon\": \"fa-code\", \"title\": \"Frontend Mastery\"}, {\"desc\": \"DOM events, asynchronous fetch calls, REST APIs, JSON, and dynamic client logic.\", \"icon\": \"fa-brands fa-js\", \"title\": \"JavaScript (ES6+)\"}, {\"desc\": \"PHP 8 OOP architecture, secure session handling, routing, and REST API development.\", \"icon\": \"fa-server\", \"title\": \"Backend Engineering\"}, {\"desc\": \"MySQL normalization, prepared statements, indexing, Git versioning, and cloud hosting.\", \"icon\": \"fa-database\", \"title\": \"Database & Cloud\"}]','[{\"num\": \"01\", \"title\": \"Modern UI Architecture (HTML5 & Advanced CSS3)\", \"topics\": [\"CSS Grid & Flexbox mastery\", \"Bootstrap 5 components\", \"Mobile-first workflows\", \"SVG & Canvas\"], \"duration\": \"4 Weeks\"}, {\"num\": \"02\", \"title\": \"Core & Advanced JavaScript (ES6+)\", \"topics\": [\"Async/Await & Promises\", \"Fetch API integrations\", \"DOM manipulation\", \"Modern tooling\"], \"duration\": \"5 Weeks\"}, {\"num\": \"03\", \"title\": \"Server-Side Engineering with PHP & OOP\", \"topics\": [\"Object-oriented design\", \"MVC architecture\", \"Authentication & JWT\", \"RESTful API creation\"], \"duration\": \"5 Weeks\"}, {\"num\": \"04\", \"title\": \"Database Design & Optimization (MySQL)\", \"topics\": [\"Schema design\", \"Complex joins & indexing\", \"Prepared statements\", \"Security against SQLi\"], \"duration\": \"4 Weeks\"}, {\"num\": \"05\", \"title\": \"Fullstack Capstone & Cloud Production Deployment\", \"topics\": [\"Git/GitHub workflows\", \"Apache/Nginx configuration\", \"SSL setup\", \"Live production deployment\"], \"duration\": \"6 Weeks\"}]','[{\"role\": \"Full Stack Developer\", \"demand\": \"Surging\", \"package\": \"₹5.5 - 12 LPA\"}, {\"role\": \"Frontend Engineer\", \"demand\": \"Very High\", \"package\": \"₹4.5 - 9 LPA\"}, {\"role\": \"PHP / Backend Developer\", \"demand\": \"High\", \"package\": \"₹4.8 - 9.5 LPA\"}, {\"role\": \"Web Application Consultant\", \"demand\": \"High\", \"package\": \"₹6.0 - 11 LPA\"}]','[\"VS Code\", \"GitHub\", \"Bootstrap 5\", \"Postman\", \"MySQL Workbench\", \"cPanel / Cloud\"]','active','2026-10-28 18:37:07','2026-10-28 18:54:57'),(5,4,'Growth & Digital Marketing Strategy','digital-marketing-strategy','Engineer customer acquisition funnels using Google Ads, Technical SEO, Meta Ads, and GA4.','High Growth','assets/images/course/digita.jpeg','4.9','780+','4 Months','8 hrs / week','Live Campaign Workshops',15999.00,'10+2 / Any Graduate','English & Hindi','₹4.2L - ₹10L / yr','95% Placed','Transform clicks into measurable revenue. Learn data-backed search engine optimization, PPC bidding algorithms, paid social media funnel strategies, and full-funnel retention marketing using modern analytics.','[{\"val\": \"₹50K+\", \"label\": \"Live Ad Spends Managed\"}, {\"val\": \"95%\", \"label\": \"Hiring Rate\"}, {\"val\": \"15+\", \"label\": \"Live Client Projects\"}, {\"val\": \"Google\", \"label\": \"Certifications\"}]','[{\"desc\": \"Keyword intent modeling, link building architectures, schema markups, and Core Web Vitals.\", \"icon\": \"fa-magnifying-glass-chart\", \"title\": \"Technical & On-Page SEO\"}, {\"desc\": \"Search ads, Display networks, Quality Score optimization, and high-converting copy.\", \"icon\": \"fa-rectangle-ad\", \"title\": \"PPC & Google Ads\"}, {\"desc\": \"Custom audiences, lookalike scaling, creative fatigue rotation, and Pixel event tracking.\", \"icon\": \"fa-brands fa-meta\", \"title\": \"Performance Meta Ads\"}, {\"desc\": \"Attribution models, UTM tracking systems, funnel drop-off analysis, and ROI measurement.\", \"icon\": \"fa-chart-simple\", \"title\": \"Analytics & Conversion (GA4)\"}]','[{\"num\": \"01\", \"title\": \"Search Engine Optimization (SEO) Masterclass\", \"topics\": [\"Keyword research tools\", \"Technical audits\", \"Backlink strategies\", \"Local SEO & GMB\"], \"duration\": \"3 Weeks\"}, {\"num\": \"02\", \"title\": \"Google Ads & Search Marketing (SEM)\", \"topics\": [\"Bid strategy modeling\", \"Ad copy testing\", \"Negative keyword lists\", \"Conversion tracking\"], \"duration\": \"4 Weeks\"}, {\"num\": \"03\", \"title\": \"Meta Advertising (Instagram & Facebook Ads)\", \"topics\": [\"Campaign budget optimization\", \"Catalog sales ads\", \"Retargeting funnels\", \"UGC creative framework\"], \"duration\": \"4 Weeks\"}, {\"num\": \"04\", \"title\": \"Email Marketing, Automation & CRM\", \"topics\": [\"Drip campaigns\", \"Lead nurture sequences\", \"Mailchimp automation\", \"List segmentation\"], \"duration\": \"2 Weeks\"}, {\"num\": \"05\", \"title\": \"Web Analytics (GA4) & Final Capstone Campaign\", \"topics\": [\"Google Tag Manager\", \"GA4 custom reports\", \"Live client ROAS optimization\"], \"duration\": \"3 Weeks\"}]','[{\"role\": \"Performance Marketer\", \"demand\": \"Surging\", \"package\": \"₹5.0 - 11 LPA\"}, {\"role\": \"SEO Specialist\", \"demand\": \"High\", \"package\": \"₹4.2 - 8.5 LPA\"}, {\"role\": \"Social Media Growth Strategist\", \"demand\": \"High\", \"package\": \"₹4.0 - 7.5 LPA\"}, {\"role\": \"Digital Marketing Manager\", \"demand\": \"Very High\", \"package\": \"₹6.5 - 14 LPA\"}]','[\"Google Ads\", \"Meta Ads Manager\", \"Semrush / Ahrefs\", \"Google Analytics 4\", \"Canva Pro\"]','active','2026-10-28 18:37:07','2026-10-28 18:54:57'),(6,1,'Aviation & Airport Operations Management','airport-management','Launch an exhilarating global career in commercial airport operations and aviation customer relations.','Global Aviation','assets/images/course/avation.webp','4.9','600+','1 Year','10 hrs / week','Campus + Live Mock Simulators',24999.00,'10+2 in Any Stream','English & Hindi','₹4.0L - ₹9.0L / yr','92% Placed','Prepare for prestigious positions within domestic and international airlines and airport terminal operations. Master aviation protocols, passenger check-in handling, ramp safety, cargo operations, and hospitality grooming.','[{\"val\": \"100%\", \"label\": \"Airport Mock Drills\"}, {\"val\": \"92%\", \"label\": \"Airline Placements\"}, {\"val\": \"IATA\", \"label\": \"Standard Guidelines\"}, {\"val\": \"Global\", \"label\": \"Career Horizon\"}]','[{\"desc\": \"Boarding gate dispatch, passenger handling systems, and VIP protocol coordination.\", \"icon\": \"fa-plane-departure\", \"title\": \"Terminal Operations\"}, {\"desc\": \"Airway bills, dangerous goods handling, and airside cargo warehouse logistics.\", \"icon\": \"fa-boxes-packing\", \"title\": \"Air Cargo & Logistics\"}, {\"desc\": \"Runway protocols, emergency passenger evacuation procedures, and security checks.\", \"icon\": \"fa-shield-halved\", \"title\": \"Aviation Safety & DGCA\"}, {\"desc\": \"Aviation phonetic code, diplomatic etiquette, voice modulation, and intercultural care.\", \"icon\": \"fa-person-dress\", \"title\": \"Grooming & Communication\"}]','[{\"num\": \"01\", \"title\": \"Civil Aviation Landscape & Regulations\", \"topics\": [\"DGCA & ICAO norms\", \"Airport authority roles\", \"Terminal classification\", \"Aviation history\"], \"duration\": \"6 Weeks\"}, {\"num\": \"02\", \"title\": \"Passenger Services & Check-in Procedures\", \"topics\": [\"Boarding pass systems\", \"Baggage handling rules\", \"Special assistance care\", \"Gate operations\"], \"duration\": \"8 Weeks\"}, {\"num\": \"03\", \"title\": \"Air Cargo Logistics & Dangerous Goods\", \"topics\": [\"DGR categorization\", \"Cargo manifests\", \"ULD loading protocols\", \"Customs documentation\"], \"duration\": \"6 Weeks\"}, {\"num\": \"04\", \"title\": \"Aviation Personality, Grooming & Etiquette\", \"topics\": [\"Professional dress code\", \"Public announcements\", \"Crisis communication\", \"Stress management\"], \"duration\": \"6 Weeks\"}, {\"num\": \"05\", \"title\": \"Airport Simulation & Airline Interview Drive\", \"topics\": [\"Live terminal mock drills\", \"Aviation group discussions\", \"Airline interview panels\"], \"duration\": \"6 Weeks\"}]','[{\"role\": \"Airport Ground Staff\", \"demand\": \"Very High\", \"package\": \"₹3.8 - 6.5 LPA\"}, {\"role\": \"Passenger Service Executive\", \"demand\": \"High\", \"package\": \"₹4.0 - 7.0 LPA\"}, {\"role\": \"Air Cargo Operations Officer\", \"demand\": \"High\", \"package\": \"₹4.2 - 7.5 LPA\"}, {\"role\": \"Cabin Crew Candidate\", \"demand\": \"Surging\", \"package\": \"₹5.5 - 10 LPA\"}]','[\"Amadeus Basics\", \"SITA Baggage Systems\", \"Aviation Phonetics\", \"Radio Protocol Drills\"]','active','2026-10-28 18:37:07','2026-10-28 18:54:57');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_interest` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','contacted','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inquiries`
--

LOCK TABLES `inquiries` WRITE;
/*!40000 ALTER TABLE `inquiries` DISABLE KEYS */;
/*!40000 ALTER TABLE `inquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qualification` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhaar_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'STU-2026-8174','Mohit Verma','mohit.v@example.com','9898989898',NULL,'Full Stack Web Technology','BCA','1234-5678-9012',NULL,'active','2026-10-28 18:31:56'),(2,'STU-2026-3406','Vikram Rajput','vikram.r@example.com','9812345678',NULL,'Full Stack Web Engineering','B.Tech','9988-7766-5544',NULL,'active','2026-10-28 18:47:07'),(3,'STU-2026-1610','nikhil','abcd@gmail.com','06676767677',NULL,'Advanced Management Studies','Bachelor\'s Degree','5345 - 2345 - 3425','uploads/students/nikhil_SGI-2026-96952/student_photo.png','active','2026-10-28 19:02:31');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` int NOT NULL DEFAULT '1',
  `smtp_host` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT 'smtp.gmail.com',
  `smtp_port` int DEFAULT '587',
  `smtp_user` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `smtp_pass` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `smtp_encryption` enum('tls','ssl','none') COLLATE utf8mb4_unicode_ci DEFAULT 'tls',
  `smtp_from_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT 'admissions@skillgrowinstitution.in',
  `smtp_from_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT 'Skill Grow Institution',
  `smtp_mode` enum('test','live') COLLATE utf8mb4_unicode_ci DEFAULT 'test',
  `razorpay_key_id` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `razorpay_key_secret` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `razorpay_mode` enum('test','live') COLLATE utf8mb4_unicode_ci DEFAULT 'test',
  `razorpay_status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `admission_fee` decimal(10,2) DEFAULT '999.00',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'smtp.gmail.com',587,'','','tls','admissions@skillgrowinstitution.in','Skill Grow Institution','test','','','test','active',999.00,'2026-10-28 18:11:47');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-29  1:28:04
