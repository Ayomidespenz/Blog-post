# Daily-Gist Blog Platform

A modern, responsive blog platform built with PHP and MySQL, featuring user authentication, post creation, and social media-like interactions.

## Features

- User Authentication (Login/Register)
- Profile Management with Profile Pictures
- Post Creation with Images
- Like and Repost Functionality
- Comment System
- Dashboard for User Posts
- Responsive Design
- Modern UI with Bootstrap 5
- Secure File Uploads
- Session Management
- Error Handling and Validation

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx Web Server
- XAMPP/WAMP (Recommended for local development)
- Modern Web Browser (Chrome, Firefox, Safari, Edge)

## Installation

1. Clone the repository to your web server directory:
```bash
git clone https://github.com/yourusername/daily-gist.git
```

2. Create a MySQL database:
```sql
CREATE DATABASE daily_gist;
```

3. Import the database schema:
```bash
mysql -u username -p daily_gist < database/schema.sql
```

4. Configure database connection:
- Edit `config/db_connect.php` with your database credentials
- Ensure proper permissions for the uploads directory

5. Set up the uploads directory:
```bash
mkdir -p uploads/{posts,profile_pictures}
chmod -R 777 uploads
```

6. Configure web server:
- Ensure mod_rewrite is enabled for Apache
- Set proper document root permissions

## Directory Structure

```
blog_platform/
├── config/
│   └── db_connect.php
├── templates/
│   ├── auth.php
│   ├── dashboard.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── nav.php
│   ├── post_create.php
│   ├── post_delete.php
│   ├── post_edit.php
│   ├── profile.php
│   ├── register.php
│   └── templates/
├── uploads/
│   ├── posts/
│   └── profile_pictures/
└── README.md
```

## Database Schema

The database schema includes tables for:
- Users (authentication)
- Posts (blog content)
- Comments (post interactions)
- Likes (post engagement)
- Reposts (content sharing)

## Security Features

- Password hashing
- Prepared statements
- Input validation
- CSRF protection
- File upload validation
- Session management
- XSS protection

## Usage

1. Create an account or login:
   - Navigate to `/login.php`
   - Register at `/register.php`

2. Create Posts:
   - Go to `/post_create.php`
   - Add title, content, and optional image

3. Manage Profile:
   - Visit `/profile.php`
   - Update username, bio, and profile picture

4. View Posts:
   - Main page: `/index.php`
   - User dashboard: `/dashboard.php`

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please:
1. Check the documentation
2. Search existing issues
3. Create a new issue if needed

## Acknowledgments

- Bootstrap 5 for UI components
- Font Awesome for icons
- jQuery for DOM manipulation
- MySQL for database
- PHP for backend logic
