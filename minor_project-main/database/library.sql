-- ============================================================
-- Smart Library Management System - Database Schema
-- BCA 2nd Semester Minor Project
-- ============================================================

CREATE DATABASE IF NOT EXISTS smart_library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_library;

-- ============================================================
-- Table: admins
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: users (students)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    reg_no VARCHAR(50) UNIQUE,
    course VARCHAR(100) DEFAULT 'BCA',
    semester VARCHAR(20),
    status ENUM('active','inactive','blocked') DEFAULT 'active',
    profile_pic VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: categories
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: books
-- ============================================================
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(150) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    category_id INT,
    publisher VARCHAR(150),
    year YEAR,
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    description TEXT,
    cover_image VARCHAR(255) DEFAULT 'default_book.png',
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ============================================================
-- Table: issued_books
-- ============================================================
CREATE TABLE IF NOT EXISTS issued_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    status ENUM('issued','returned','overdue') DEFAULT 'issued',
    issued_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- ============================================================
-- Table: fines
-- ============================================================
CREATE TABLE IF NOT EXISTS fines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    issued_book_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) DEFAULT 0.00,
    paid_status ENUM('unpaid','paid') DEFAULT 'unpaid',
    paid_date DATE DEFAULT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (issued_book_id) REFERENCES issued_books(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- Table: notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    admin_id INT,
    message TEXT NOT NULL,
    type ENUM('info','warning','success','error') DEFAULT 'info',
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Table: contact_messages
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Admin (password: admin123)
INSERT INTO admins (name, email, password, phone) VALUES
('Admin User', 'admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210'),
('Dr. Sharma', 'sharma@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543211');

-- Students (password: student123)
INSERT INTO users (name, email, password, phone, address, reg_no, course, semester) VALUES
('Rahul Sharma', 'rahul@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876501001', 'Delhi, India', 'BCA/2024/001', 'BCA', '2nd'),
('Priya Singh', 'priya@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876501002', 'Mumbai, India', 'BCA/2024/002', 'BCA', '2nd'),
('Amit Kumar', 'amit@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876501003', 'Kolkata, India', 'BCA/2024/003', 'BCA', '2nd'),
('Sneha Patel', 'sneha@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876501004', 'Ahmedabad, India', 'BCA/2024/004', 'BCA', '4th'),
('Rohit Verma', 'rohit@student.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876501005', 'Chennai, India', 'BCA/2024/005', 'BCA', '6th');

-- Categories
INSERT INTO categories (name, description) VALUES
('Computer Science', 'Books related to programming, algorithms, and software engineering'),
('Mathematics', 'Books on calculus, algebra, statistics and discrete math'),
('Physics', 'Books covering mechanics, thermodynamics, and modern physics'),
('Literature', 'Fiction, non-fiction, poetry and literary works'),
('Business', 'Books on management, economics, and entrepreneurship'),
('Self-Help', 'Personal development and motivational books'),
('History', 'Books on world history and civilizations'),
('Science Fiction', 'Futuristic and speculative fiction');

-- Books
INSERT INTO books (title, author, isbn, category_id, publisher, year, total_copies, available_copies, description, location) VALUES
('Introduction to Algorithms', 'Cormen, Leiserson, Rivest', '978-0262033848', 1, 'MIT Press', 2022, 5, 4, 'The definitive guide to algorithms and data structures.', 'CS-A1'),
('Clean Code', 'Robert C. Martin', '978-0132350884', 1, 'Prentice Hall', 2008, 3, 3, 'A handbook of agile software craftsmanship.', 'CS-A2'),
('JavaScript: The Good Parts', 'Douglas Crockford', '978-0596517748', 1, 'O\'Reilly', 2008, 4, 3, 'Unearthing the excellence in JavaScript.', 'CS-A3'),
('Python Crash Course', 'Eric Matthes', '978-1593279288', 1, 'No Starch Press', 2019, 6, 5, 'A hands-on, project-based introduction to Python.', 'CS-A4'),
('Database System Concepts', 'Silberschatz, Korth', '978-0078022159', 1, 'McGraw-Hill', 2020, 4, 2, 'Comprehensive guide to database systems.', 'CS-B1'),
('Calculus: Early Transcendentals', 'James Stewart', '978-1285741550', 2, 'Cengage', 2015, 5, 5, 'Industry standard calculus textbook.', 'MATH-A1'),
('Discrete Mathematics', 'Kenneth Rosen', '978-0073383095', 2, 'McGraw-Hill', 2018, 4, 4, 'Discrete mathematics and its applications.', 'MATH-A2'),
('Linear Algebra', 'Gilbert Strang', '978-0980232776', 2, 'Wellesley Cambridge', 2016, 3, 3, 'Introduction to linear algebra.', 'MATH-A3'),
('Concepts of Physics Vol 1', 'H.C. Verma', '978-8177091878', 3, 'Bharati Bhawan', 2010, 8, 7, 'Essential physics for science students.', 'PHY-A1'),
('University Physics', 'Young & Freedman', '978-0133969290', 3, 'Pearson', 2015, 5, 4, 'Comprehensive university-level physics.', 'PHY-A2'),
('To Kill a Mockingbird', 'Harper Lee', '978-0061935466', 4, 'Harper Perennial', 2002, 3, 3, 'Pulitzer Prize winning novel about justice.', 'LIT-A1'),
('The Great Gatsby', 'F. Scott Fitzgerald', '978-0743273565', 4, 'Scribner', 2004, 4, 4, 'Classic American novel of the Jazz Age.', 'LIT-A2'),
('Thinking, Fast and Slow', 'Daniel Kahneman', '978-0374533557', 5, 'Farrar Straus', 2013, 3, 2, 'Groundbreaking work on decision making.', 'BUS-A1'),
('Rich Dad Poor Dad', 'Robert Kiyosaki', '978-1612680194', 5, 'Plata Publishing', 2017, 5, 3, 'What the rich teach their kids about money.', 'BUS-A2'),
('Atomic Habits', 'James Clear', '978-0735211292', 6, 'Avery', 2018, 6, 4, 'An easy way to build good habits.', 'SELF-A1'),
('The 7 Habits of Highly Effective People', 'Stephen Covey', '978-1982137274', 6, 'Simon & Schuster', 2020, 4, 4, 'Powerful lessons in personal change.', 'SELF-A2'),
('A Brief History of Time', 'Stephen Hawking', '978-0553380163', 3, 'Bantam Books', 1998, 3, 3, 'From the Big Bang to black holes.', 'PHY-B1'),
('Sapiens', 'Yuval Noah Harari', '978-0062316097', 7, 'Harper', 2015, 5, 4, 'A brief history of humankind.', 'HIS-A1'),
('Dune', 'Frank Herbert', '978-0441013593', 8, 'Ace Books', 2019, 3, 3, 'Epic science fiction masterpiece.', 'SCI-A1'),
('The Hitchhiker\'s Guide to the Galaxy', 'Douglas Adams', '978-0345391803', 8, 'Del Rey', 1995, 4, 4, 'Comedic science fiction series.', 'SCI-A2'),
('Operating System Concepts', 'Silberschatz, Galvin', '978-1118063330', 1, 'Wiley', 2018, 5, 3, 'The definitive OS textbook - Dinosaur Book.', 'CS-C1'),
('Computer Networks', 'Andrew Tanenbaum', '978-0133594492', 1, 'Pearson', 2010, 4, 4, 'Comprehensive guide to computer networks.', 'CS-C2'),
('Probability and Statistics', 'Sheldon Ross', '978-0123736352', 2, 'Academic Press', 2009, 3, 3, 'Introduction to probability and statistics.', 'MATH-B1');

-- Issued Books (sample)
INSERT INTO issued_books (user_id, book_id, issue_date, due_date, status) VALUES
(1, 1, CURDATE() - INTERVAL 5 DAY, CURDATE() + INTERVAL 9 DAY, 'issued'),
(1, 3, CURDATE() - INTERVAL 3 DAY, CURDATE() + INTERVAL 11 DAY, 'issued'),
(2, 5, CURDATE() - INTERVAL 20 DAY, CURDATE() - INTERVAL 6 DAY, 'overdue'),
(3, 7, CURDATE() - INTERVAL 12 DAY, CURDATE() - INTERVAL 2 DAY, 'returned'),
(4, 15, CURDATE() - INTERVAL 8 DAY, CURDATE() + INTERVAL 6 DAY, 'issued'),
(5, 18, CURDATE() - INTERVAL 1 DAY, CURDATE() + INTERVAL 13 DAY, 'issued');

-- Update book available copies
UPDATE books SET available_copies = available_copies - 1 WHERE id IN (1, 3, 5, 15, 18);
UPDATE issued_books SET return_date = CURDATE() - INTERVAL 2 DAY WHERE id = 4;

-- Fines (for overdue)
INSERT INTO fines (issued_book_id, user_id, amount, paid_status) VALUES
(3, 2, 12.00, 'unpaid');

-- Notifications
INSERT INTO notifications (user_id, message, type) VALUES
(1, 'Your book "Introduction to Algorithms" is due in 9 days.', 'info'),
(2, 'Your book "Database System Concepts" is overdue! Fine: ₹12', 'warning'),
(3, 'Book "Discrete Mathematics" returned successfully.', 'success'),
(4, 'Your book "Atomic Habits" is due in 6 days.', 'info');
