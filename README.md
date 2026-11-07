# Wish Group Resources – Company Website

A company website developed during my IT internship at **Wish Group Resources (WGR)**. The project was built using HTML, CSS, JavaScript, and PHP, with additional backend functionality for contact submissions, CV applications, and administrative management.

## 🌐 Project Overview

The WGR Company Website is designed to establish an online presence for Wish Group Resources by providing visitors with company information, services, and contact details.

In addition to the public-facing website, the project includes an administrative management system that allows authorized administrators to manage submitted enquiries and CV applications.

## 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Website structure and content |
| CSS3 | Styling and responsive layouts |
| JavaScript | Frontend interactions and functionality |
| PHP | Server-side processing and backend functionality |
| JSON | Multilingual content management |
| PHPMailer | Email functionality |
| MySQL / Database | To be confirmed based on database configuration |

## ✨ Key Features

### Public Website
- **Responsive Design** – Adapts the website layout to different screen sizes and devices.
- **Navigation System** – Provides intuitive navigation across website sections.
- **Multilingual Support** – Supports English and Chinese languages.
- **Contact Form** – Allows visitors to submit enquiries.
- **CV Submission** – Allows applicants to submit their CV through the website.

### Admin Management System
- **Admin Authentication** – Administrator login and registration.
- **Admin Dashboard** – Centralized interface for administrative management.
- **Password Management** – Change password and password recovery functionality.
- **Contact Management** – Access and download submitted contact enquiries.
- **CV Management** – Access and download submitted CV applications.
- **Admin Account Management** – Manage administrator accounts.

### Email Integration
- PHPMailer integration for email-related functionality.
- Server-side PHP processing for website form submissions.

## 📂 Project Structure

```text
wgr-website/
│
├── admin/                      # Admin management system
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── register_admin.php
│   ├── change_password.php
│   ├── forgot_password.php
│   ├── reset_password.php
│   ├── reset_admin_password.php
│   ├── delete_admin.php
│   ├── download_contacts.php
│   ├── download_cvs.php
│   ├── db.php
│   ├── login.css
│   └── styles.css
│
├── images/                     # Website images and visual assets
│
├── lang/                       # Language configuration
│   ├── en.json
│   └── zh.json
│
├── PHPMailer/                  # Email integration library
│
├── uploads/                    # Uploaded CV documents (excluded from Git)
│
├── videos/                     # Website video assets
│
├── db.php                      # Database configuration
├── index.html                  # Main website page
├── index.css                   # Main stylesheet
├── index.js                    # Frontend JavaScript
├── submit_contact.php          # Contact form processing
├── submit_cv.php               # CV submission processing
│
└── README.md
```

## ⚙️ Installation and Setup

### Prerequisites

- PHP
- Apache server (XAMPP recommended)
- Web browser
- Visual Studio Code or another code editor

### Getting Started

1. Clone the repository:

   ```bash
   git clone YOUR_REPOSITORY_URL
   ```

2. Move the project folder into the XAMPP `htdocs` directory.

3. Start Apache using the XAMPP Control Panel.

4. Configure the necessary PHP settings and database connection if required.

5. Configure PHPMailer and email credentials if email functionality is enabled.

6. Open your browser and navigate to:

   ```text
   http://localhost/wgr-website/
   ```

## 🎓 Internship Experience

Developed during my IT internship at Wish Group Resources.

Throughout this project, I gained practical experience in:

- Developing responsive company websites using HTML, CSS, and JavaScript.
- Implementing backend functionality using PHP.
- Developing administrative interfaces and authentication functionality.
- Handling contact form submissions and CV uploads.
- Integrating email functionality using PHPMailer.
- Implementing multilingual website content using JSON.
- Organizing and maintaining a full-stack web development project.

## 👨‍💻 Author

**Low Shao Xuan**

- GitHub: [@Lowshaoxuan](https://github.com/Lowshaoxuan)

---

*Developed as part of my IT internship at Wish Group Resources.*
