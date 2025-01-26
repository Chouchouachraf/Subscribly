# Subscribly - Subscription Management System

A comprehensive subscription management system that helps users track and manage all their subscriptions in one place.

## Features

- User authentication
- Subscription management
- Reminder system
- Dashboard with subscription overview
- Payment history tracking
- Search and filter functionality
- Bank integration (planned)

## Tech Stack

- Frontend: HTML, CSS (Tailwind CSS), JavaScript (Alpine.js)
- Backend: PHP
- Database: MySQL
- Authentication: JWT

## Installation

1. Install XAMPP on your system
2. Clone this repository to your `htdocs` folder
3. Import the database schema from `database/subscribly.sql`
4. Configure your database connection in `config/database.php`
5. Access the application through `http://localhost/Subscribly`

## Development Setup

1. Install dependencies:
   ```bash
   npm install    # For Tailwind CSS and Alpine.js
   ```

2. Start the development server:
   ```bash
   npm run dev    # For Tailwind CSS compilation
   ```

## Project Structure

```
Subscribly/
├── assets/          # Static assets (CSS, JS, images)
├── config/          # Configuration files
├── database/        # Database schema and migrations
├── includes/        # PHP includes and classes
├── public/          # Publicly accessible files
└── templates/       # HTML templates
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License.
