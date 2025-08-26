# Restaurant Dashboard Backend

This is a core PHP backend for a Restaurant Analytics Dashboard. It provides RESTful API endpoints for restaurant listing, analytics, and order trends, using a MySQL database. Designed for use with XAMPP (Apache + MySQL).

## Features
- List restaurants with search, filter, and pagination
- Analytics endpoints for daily orders, revenue, average order value, and peak order hours
- Filter analytics by date, order amount, and hour range
- CORS enabled for frontend integration

## Requirements
- PHP 7.4+
- MySQL
- XAMPP (recommended for local development)

## Setup Instructions
1. **Clone the repository:**
   ```
   git clone https://github.com/haachico/restaurant-dashboard-backend
   ```
2. **Import the database:**
   - Use `phpMyAdmin` (usually at http://localhost/phpmyadmin) to import the provided SQL or JSON data into MySQL.
   - Create the required tables as described in the assignment or project documentation.
3. **Configure database connection:**
   - Edit `config/db.php` with your MySQL credentials.
4. **Start Apache and MySQL via XAMPP.**
5. **Access API endpoints:**
   - Example: `http://localhost/api/restaurants.php`
   - Example: `http://localhost/api/order_trends.php?restaurant_id=1&start_date=2025-06-22&end_date=2025-06-28`


## Notes
- If port 80 is unavailable, you may need to run Apache on a different port (e.g., 8080) and update your URLs accordingly.
- All endpoints return JSON responses.
- CORS headers are enabled for local frontend development.

## Troubleshooting
- If you get a database connection error, check your credentials in `config/db.php` and ensure MySQL is running.
- If you get a 404, make sure your API files are in the correct directory (e.g., `htdocs/restaurants-dashboard-backend/api/`).

## License
This project is for demonstration and assignment purposes.
