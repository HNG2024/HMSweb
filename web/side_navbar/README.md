# Hotel Management System

This is a simple Hotel Management System created using PHP, MySQL, and CSS. It allows you to book rooms and manage room bookings.

## Features
- Book rooms with various details like guest information, check-in/check-out dates, payment details, etc.
- Manage room bookings by viewing, editing, and deleting existing bookings.
- Basic responsive design for desktop and mobile.

## Setup Instructions
1. Clone the repository or download the files.
2. Import the `sql/create_database.sql` file into your MySQL database to create the necessary database and tables.
3. Update the database connection details in `php/db_connection.php`.
4. Open `index.php` in your browser to access the home page.

## File Structure
- `css/`: Contains the CSS files for styling.
- `images/`: Place any images you want to use in this folder.
- `js/`: Contains optional JavaScript files for additional functionalities.
- `php/`: Contains the PHP scripts for booking and managing rooms.
- `sql/`: Contains the SQL file to set up the database.
- `index.php`: The main home page or dashboard.
- `README.md`: Documentation for the project.

## Future Enhancements
- Add user authentication and roles (e.g., admin, manager).
- Integrate with a payment gateway for online payments.
- Add reporting features to generate booking summaries.
