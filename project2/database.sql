-- COS10026 Group G05 | Basket Digital Project 2
-- Run in phpMyAdmin for a manual installation, or let the local site initialise it on first use.
-- Demo data only. Avoid entering real applicant information.
CREATE DATABASE IF NOT EXISTS basket_digital_project2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE basket_digital_project2;

CREATE TABLE IF NOT EXISTS jobs (
    reference CHAR(5) PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    salary VARCHAR(100) NOT NULL,
    reporting_to VARCHAR(120) NOT NULL,
    responsibilities TEXT NOT NULL,
    essential_requirements TEXT NOT NULL,
    preferable_requirements TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_alt VARCHAR(255) NOT NULL,
    salary_source_url VARCHAR(500) NULL,
    salary_source_name VARCHAR(200) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_ref CHAR(5) NOT NULL,
    first_name VARCHAR(20) NOT NULL,
    last_name VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(24) NOT NULL,
    street VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state VARCHAR(10) NOT NULL,
    postcode CHAR(4) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skills TEXT NOT NULL,
    other_skills TEXT NULL,
    status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_job_ref (job_ref),
    INDEX idx_names (last_name, first_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS about (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    display_order TINYINT UNSIGNED NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    part1_contribution TEXT NOT NULL,
    part2_contribution TEXT NOT NULL,
    quote_original VARCHAR(255) NOT NULL,
    quote_translation VARCHAR(255) NOT NULL,
    hometown VARCHAR(120) NOT NULL,
    dream_job VARCHAR(120) NOT NULL,
    coding_snack VARCHAR(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO jobs (reference,title,description,salary,reporting_to,responsibilities,essential_requirements,preferable_requirements,image_path,image_alt,salary_source_url,salary_source_name) VALUES
('FE902','Frontend React Engineer','Build fast and accessible customer-facing retail experiences for online shoppers.','RM 84,000 – RM 168,000 per annum','Head of Digital Storefronts',
'["Develop accessible, responsive user interface components.","Improve performance for mobile shoppers.","Collaborate with the design and product teams."]',
'["Three or more years of HTML and CSS development experience.","Understanding of WCAG and accessible web practices."]',
'["Experience with React and component-based interfaces."]','images/job_frontend.jpg','Frontend engineer working with code on computer screens','https://www.randstad.com.my/most-in-demand-jobs-malaysia-emerging-technology/','Randstad Malaysia career guide'),
('MS411','Online Merchandising Specialist','Manage and improve product listings and discovery for Basket Digital shoppers.','RM 54,000 – RM 90,000 per annum','E-Commerce Director',
'["Maintain accurate product listings and inventory updates.","Analyse engagement trends to improve product discovery.","Write useful, searchable product descriptions."]',
'["Strong analytical and communication skills.","Experience using a content management system."]',
'["Previous experience in online retail or e-commerce."]','images/job_merch.jpg','Merchandising specialist analysing retail data on a laptop','https://www.clickacademyasia.com/post/digital-marketing-salary-guide-malaysia-2026-benchmarks-and-career-growth','Digital marketing salary guide')
ON DUPLICATE KEY UPDATE reference=VALUES(reference);

-- Required classroom HR account: admin / admin. Password is stored as a hash.
INSERT INTO users (username,password_hash) VALUES ('admin', '$2y$12$Il98zMWm3Dl/tOsv9TC52eIowcY.o1/8idW.UbP5sFJ/M3A8HO8FC')
ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash);

INSERT INTO about (id,display_order,student_name,student_id,part1_contribution,part2_contribution,quote_original,quote_translation,hometown,dream_job,coding_snack) VALUES
(1,1,'Afnan Al Sanim','106702360','Home page, About page and shared styling; contributed to accessibility and integration work.','Group G05 Part 2: developed a shared PHP page layout, built a database-connected team page, and reviewed the homepage and accessibility.','কঠিন পরিশ্রমই সফলতার চাবিকাঠি।','Hard work is the key to success.','Chittagong','Full Stack Developer','Chocolate Milk'),
(2,2,'Tajuddin Munshi','105729193','Jobs page and application form, including form layout and validation work.','Group G05 Part 2: converted job listings to database content, connected the application form to MySQL, and tested HR management features.','চেষ্টা করলে সব সম্ভব।','Everything is possible if you try.','Dhaka','Data Scientist','Chips & Cola')
ON DUPLICATE KEY UPDATE part1_contribution=VALUES(part1_contribution), part2_contribution=VALUES(part2_contribution), student_name=VALUES(student_name);