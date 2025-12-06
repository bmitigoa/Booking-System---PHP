# 🎓 Lab Booking System

A lightweight, PHP-based Lab Booking System built using **PHP**, **MySQL**, and **HTML/CSS**.  
The system allows administrators to manage labs and events, while users can browse upcoming events and make bookings easily.

---

## 📌 Features

### Admin Features
- Admin login system  
- Add, edit, and delete labs  
- Add, edit, and delete events  
- View all labs and events  

### User Features
- View upcoming events  
- Book any available event  
- Booking confirmation  

### UI / Frontend
- Simple and clean responsive design  
- Background image support  
- Single-page style navigation  

---

## ⚙️ Technologies Used
- PHP (Procedural)  
- MySQL + mysqli  
- HTML, CSS  
- XAMPP / WAMP / MAMP for local server  

---

## 💾 Database Setup

Before running the application, create the MySQL database and tables.

**Create Database**

```sql
CREATE DATABASE labbooking;
USE labbooking;

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Labs table
CREATE TABLE labs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    lab_id INT NOT NULL,
    FOREIGN KEY (lab_id) REFERENCES labs(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);



