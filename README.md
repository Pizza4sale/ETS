Expense Tracker
Project Overview
The Expense Tracker is a web application designed to help users manage and track their expenses efficiently. Users can add, view, edit, and delete expenses, categorize them, and generate reports to analyze their spending habits over time.

Table of Contents
Installation Instructions
Features
Usage
Code Structure
Database Structure
Contributions
License
Installation Instructions
To set up the Expense Tracker on your local machine, follow these steps:

Clone the Repository:

bash
Copy code
git clone https://github.com/Pizza4sale/TMCP.git
Setup a Local Server: Ensure you have a local server environment like XAMPP, WAMP, or MAMP installed.

Create a Database:

Open your database management tool (e.g., phpMyAdmin).
Create a new database named ets.
Import Database Structure:

Execute the SQL scripts to create tables. (Provide the SQL file if necessary).
Configure Database Connection:

Open includes/head.php.
Update the database connection details if necessary.
Start the Server:

Start your local server and navigate to the project directory.
Access the Application:

Open a web browser and go to http://localhost/path_to_your_project.
Features
User Authentication: Users can register and log in securely.
Expense Management: Users can add, view, edit, and delete their expenses.
Category Management: Users can categorize their expenses for better tracking.
Budget Tracking: Users can set budgets for categories and track usage.
Reports: Users can generate expense summaries over a selected date range.
Change Password: Users can change their account passwords securely.
Responsive Design: The application is designed to be user-friendly and responsive.
Usage
User Registration:

Users can register by filling in their details on the registration page.
Logging In:

Users must log in with their credentials to access the application.
Managing Expenses:

Navigate to the expenses page to add or view expenses.
Users can filter expenses by date and category.
Generating Reports:

Users can access the reports page to filter expenses based on date ranges.
Changing Password:

Users can navigate to the settings page to change their password.
Code Structure
The project code is organized into several key components:

includes/: Contains reusable components like database connection, header, navbar, sidebar, and scripts.
index.php: The home page of the application.
login.php: Handles user login functionality.
register.php: Handles user registration.
expenses.php: Manages expense data retrieval and display.
reports.php: Generates expense reports based on user-defined date ranges.
settings.php: Allows users to change their passwords.
Database Structure
The database consists of the following tables:

users: Stores user information.

id: INT, auto-increment, primary key
username: VARCHAR(50), unique
email: VARCHAR(100), unique
password: VARCHAR(255)
created_at: TIMESTAMP, default CURRENT_TIMESTAMP
categories: Stores expense categories.

id: INT, auto-increment, primary key
category_name: VARCHAR(100)
expenses: Stores individual expense records.

id: INT, auto-increment, primary key
user_id: INT, foreign key
category_id: INT, foreign key
amount: DECIMAL(10,2)
expense_date: DATE
description: TEXT
budgets: Stores budget limits for categories.

id: INT, auto-increment, primary key
user_id: INT, foreign key
category_id: INT, foreign key
budget_limit: DECIMAL(10,2)
start_date: DATE
end_date: DATE
Contributions
Contributions to the project are welcome. Please fork the repository, make your changes, and submit a pull request.

License
This project is licensed under the MIT License - see the LICENSE file for details.

