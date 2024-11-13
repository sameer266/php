BusQueue Feedback System
A simple feedback submission page created as part of the BusQueue project. This project is designed to collect user feedback to improve the platform.

Features
Feedback form to collect user input (name, email, feedback)
Simple HTML/CSS interface
PHP script for handling feedback submissions
Installation
Prerequisites
XAMPP: For setting up the local PHP environment and MySQL database.
Git: For version control and GitHub integration (if modifying the project).
Steps
Clone the Repository

bash
Copy code
git clone https://github.com/your-username/BusQueue-Feedback.git
cd BusQueue-Feedback
Start XAMPP

Start Apache and MySQL from the XAMPP control panel.
Set Up Database

Open phpMyAdmin.
Create a new database: busqueue_feedback
Import the SQL file provided (located in database/busqueue_feedback.sql) into this database.
Configure Database in Project

If required, configure the database connection details in your PHP files (e.g., submit_feedback.php) to match your setup.
Run the Project

Place the project folder in the htdocs directory of XAMPP.
Open http://localhost/BusQueue-Feedback in your browser.
Usage
Navigate to the Feedback page to submit feedback. The input data will be stored in the busqueue_feedback database created earlier.

License
This project is open-source and available under the MIT License.

Database 
xampp bus_queue
1st==>users
2nd ==> stops
3rd ==> routes
routes :
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_number VARCHAR(50) NOT NULL,
    route VARCHAR(255) NOT NULL,
    departure_time TIME NOT NULL
);
stops:
CREATE TABLE stops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT,
    name VARCHAR(255) NOT NULL,
    lat DOUBLE NOT NULL,
    lng DOUBLE NOT NULL,
    estimated_time TIME NOT NULL,
    FOREIGN KEY (route_id) REFERENCES routes(id)
);
users:
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL
);


