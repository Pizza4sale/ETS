# Expense Tracker

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Issues](https://img.shields.io/github/issues/Pizza4sale/ETS)
![Forks](https://img.shields.io/github/forks/Pizza4sale/ETS)
![Stars](https://img.shields.io/github/stars/Pizza4sale/ETS)

## Project Overview
The Expense Tracker is a web application designed to help users manage and track their expenses efficiently. Users can add, view, edit, and delete expenses, categorize them, and generate reports to analyze their spending habits over time.

## Table of Contents
- [Installation Instructions](#installation-instructions)
- [Features](#features)
- [Usage](#usage)
- [Code Structure](#code-structure)
- [Database Structure](#database-structure)
- [Contributing](#contributing)


## Installation Instructions
To set up the Expense Tracker on your local machine, follow these steps:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Pizza4sale/ETS.git
   ```

2. **Setup a Local Server**:
   Ensure you have a local server environment like XAMPP, WAMP, or MAMP installed.

3. **Create a Database**:
   - Open your database management tool (e.g., phpMyAdmin).
   - Create a new database named `ets`.

4. **Import Database Structure**:
   - Execute the SQL scripts to create tables. (Provide the SQL file if necessary).

5. **Configure Database Connection**:
   - Open `includes/head.php`.
   - Update the database connection details if necessary.

6. **Start the Server**:
   - Start your local server and navigate to the project directory.

7. **Access the Application**:
   - Open a web browser and go to `http://localhost/path_to_your_project`.

## Features
- **User Authentication**: Secure registration and login for users.
- **Expense Management**: Add, view, edit, and delete expenses.
- **Category Management**: Categorize expenses for better tracking.
- **Budget Tracking**: Set and track budgets for different categories.
- **Reports**: Generate expense summaries over a selected date range.
- **Change Password**: Secure password management for users.
- **Responsive Design**: User-friendly interface suitable for all devices.

## Usage
1. **User Registration**: Register by filling in your details on the registration page.
2. **Logging In**: Log in with your credentials to access the application.
3. **Managing Expenses**: Add or view expenses and filter by date and category.
4. **Generating Reports**: Filter expenses based on user-defined date ranges in the reports section.
5. **Changing Password**: Change your password in the settings section.

## Code Structure
The project code is organized into several key components:
- **includes/**: Reusable components (database connection, header, navbar, sidebar, scripts).
- **index.php**: The home page of the application.
- **login.php**: Handles user login functionality.
- **register.php**: Handles user registration.
- **expenses.php**: Manages expense data retrieval and display.
- **reports.php**: Generates expense reports based on user-defined date ranges.
- **settings.php**: Allows users to change their passwords.

## Database Structure
The database consists of the following tables:
- **users**: Stores user information (ID, username, email, password, created_at).
- **categories**: Stores expense categories (ID, category_name).
- **expenses**: Stores individual expense records (ID, user_id, category_id, amount, expense_date, description).
- **budgets**: Stores budget limits for categories (ID, user_id, category_id, budget_limit, start_date, end_date).

## Contributing
Contributions are welcome! If you'd like to contribute:
1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Make your changes and commit them (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature/YourFeature`).
5. Open a Pull Request.


## License

This project is open-source and available under the [MIT License](LICENSE).

---

**Author**: https://www.facebook.com/Pizza4sale/  
**Contact**: 09195431910  
