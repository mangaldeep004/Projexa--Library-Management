# 📚 Smart Library Management System
### BCA 2nd Semester Minor Project

![PHP](https://img.shields.io/badge/PHP-8.x-777bb4?style=flat&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-f29111?style=flat&logo=mysql)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=flat&logo=javascript&logoColor=black)

---

## 📋 Project Overview

A fully functional **Smart Library Management System** developed as a Minor Project for BCA 2nd Semester. The system provides a modern, intuitive interface for managing library books, students, and transactions.

---

## ✨ Features

### 🔐 Authentication
- Admin & Student login with role-based access
- Student registration with form validation
- Session management & secure logout
- Password hashing using bcrypt

### 👨‍💼 Admin Dashboard
- 📊 Statistics cards (total books, students, issued, overdue)
- 📈 Interactive charts (monthly issues, categories, overview)
- 📚 Full Book Management (Add, Edit, Delete, Search)
- 🏷️ Category Management
- 📤 Issue Book to students
- 📥 Return Book with fine calculation
- 👥 Student Management (view, block/unblock)
- 💰 Fine Management (view, mark as paid)
- 📋 Reports & Analytics page

### 👨‍🎓 Student Dashboard
- 📖 View currently issued books with due dates
- 🔍 Browse & search all books (real-time AJAX search)
- 📚 Book history (issued, returned, overdue)
- 💰 View pending fines
- 👤 Edit profile & change password
- 🔔 Notification alerts

### 🌟 Smart Features
- 🌙 Dark mode toggle (persisted in localStorage)
- ⚡ Real-time AJAX book search
- 📊 Chart.js interactive charts
- 🔢 Animated statistics counters
- 💵 Auto fine calculation (₹2/day overdue)
- 📱 Responsive mobile-friendly design
- ✅ Client + server-side validation

---

## 🗂️ Project Structure

```
Smart-Library-Management/
├── index.php              # Home / Landing Page
├── about.php              # About Page
├── contact.php            # Contact Page
├── login.php              # Login (Admin + Student)
├── register.php           # Student Registration
├── logout.php             # Logout Handler
│
├── admin/
│   ├── dashboard.php      # Admin Dashboard
│   ├── books.php          # Book List & Delete
│   ├── add_book.php       # Add New Book
│   ├── edit_book.php      # Edit Book
│   ├── issue_book.php     # Issue Book to Student
│   ├── return_book.php    # Return Book + Fine
│   ├── issued_books.php   # All Issued Books
│   ├── students.php       # Student Management
│   ├── fines.php          # Fine Management
│   ├── categories.php     # Category Management
│   ├── reports.php        # Analytics & Reports
│   └── _sidebar.php       # Sidebar Include
│
├── student/
│   ├── dashboard.php      # Student Dashboard
│   ├── browse_books.php   # Browse & Search Books
│   ├── my_books.php       # Book History
│   ├── profile.php        # Profile & Edit
│   └── _sidebar.php       # Sidebar Include
│
├── includes/
│   ├── db.php             # Database Connection (PDO)
│   ├── auth.php           # Auth & Session Helpers
│   └── functions.php      # Utility Functions
│
├── assets/
│   ├── css/
│   │   ├── style.css      # Global Styles
│   │   └── dashboard.css  # Dashboard Styles
│   └── js/
│       ├── main.js        # Core JavaScript
│       └── dashboard.js   # Charts & Sidebar
│
├── api/
│   └── search_books.php   # AJAX Search API
│
├── database/
│   └── library.sql        # Database Schema + Sample Data
│
└── README.md              # This file
```

---

## 🛠️ Setup Instructions

### Prerequisites
- **XAMPP** (or WAMP / MAMP)
- PHP 7.4+ (PHP 8.x recommended)
- MySQL 5.7+

### Step 1: Install XAMPP
Download from https://www.apachefriends.org/ and install.

### Step 2: Copy Project Files
Copy the entire `Minor Project` folder to:
```
C:/xampp/htdocs/Minor Project/
```

### Step 3: Start XAMPP Services
Open XAMPP Control Panel and start:
- ✅ Apache
- ✅ MySQL

### Step 4: Import Database
1. Open browser → go to `http://localhost/phpmyadmin`
2. Click **"New"** → Create database named `smart_library`
3. Select `smart_library` → Click **"Import"** tab
4. Click **"Choose File"** → select `database/library.sql`
5. Click **"Go"** to import

### Step 5: Configure Database Connection
Open `includes/db.php` and verify settings:
```php
define('DB_HOST', 'localhost');   // Usually localhost
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password (empty for XAMPP)
define('DB_NAME', 'smart_library');
```

### Step 6: Run the Project
Open browser → `http://localhost/Minor Project/`

---

## 🔑 Login Credentials

### Admin Login
| Field | Value |
|-------|-------|
| Email | `admin@library.com` |
| Password | `password` |

### Student Login
| Field | Value |
|-------|-------|
| Email | `rahul@student.com` |
| Password | `password` |

> **Note:** The password hash in the SQL matches `password`. When creating new accounts via register form, bcrypt is used.

---

## 🗄️ Database Tables

| Table | Description |
|-------|-------------|
| `admins` | Library administrators |
| `users` | Registered students |
| `categories` | Book categories |
| `books` | Book catalog |
| `issued_books` | Book issue & return records |
| `fines` | Fine records |
| `notifications` | User notifications |
| `contact_messages` | Contact form submissions |

---

## 💻 Technologies Used

| Technology | Purpose |
|------------|---------|
| PHP 8 (PDO) | Server-side logic, DB queries |
| MySQL 8 | Relational database |
| HTML5 | Page structure |
| CSS3 | Styling, animations, dark mode |
| JavaScript (ES6) | Interactivity, AJAX |
| Chart.js 4 | Dashboard charts |
| Font Awesome 6 | Icons |
| Google Fonts (Inter) | Typography |

---

## 📱 Responsive Design

The UI is fully responsive:
- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1200px)
- ✅ Mobile (< 768px) with slide-in sidebar

---

## 🎨 Design Features

- **Color Theme:** Blue-Purple gradient (`#667eea` → `#764ba2`)
- **Glassmorphism** elements on hero
- **Dark Mode** with CSS variables
- **Smooth animations** and hover effects
- **Inter** font from Google Fonts
- **Modern card** UI with shadows

---

## 📝 Pages Summary

| Page | URL | Access |
|------|-----|--------|
| Home | `/` | Public |
| About | `/about.php` | Public |
| Contact | `/contact.php` | Public |
| Login | `/login.php` | Public |
| Register | `/register.php` | Public |
| Admin Dashboard | `/admin/dashboard.php` | Admin |
| Book Management | `/admin/books.php` | Admin |
| Issue Book | `/admin/issue_book.php` | Admin |
| Return Book | `/admin/return_book.php` | Admin |
| Fine Management | `/admin/fines.php` | Admin |
| Reports | `/admin/reports.php` | Admin |
| Student Dashboard | `/student/dashboard.php` | Student |
| Browse Books | `/student/browse_books.php` | Student |
| My Books | `/student/my_books.php` | Student |
| My Profile | `/student/profile.php` | Student |

---

## 🔒 Security Features

- Passwords hashed with `password_hash()` (bcrypt)
- PDO prepared statements (no SQL injection)
- `htmlspecialchars()` on all output (no XSS)
- Session-based authentication
- Role-based access control
- Input sanitization on all user inputs

---

## 👨‍💻 Developer Notes

- **Fine Rate:** ₹2 per overdue day (configurable in `functions.php`)
- **Default Loan Period:** 14 days
- **AJAX Search:** Real-time search via `api/search_books.php`
- **Charts:** Chart.js 4 via CDN

---

## 📄 License

This project is developed for educational purposes as part of BCA curriculum.

---

*Made with ❤️ for BCA Minor Project Submission 2024*
