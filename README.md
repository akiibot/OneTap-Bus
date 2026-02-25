<p align="center">
  <img src="assets/images/icon.png" alt="OneTap Bus Logo" width="120" />
</p>

<h1 align="center">🚌 OneTap Bus</h1>

<p align="center">
  <b>Your one-tap solution to find buses in Dhaka</b><br/>
  A modern, full-featured bus route finder, fare calculator, and seat reservation platform built for daily commuters in Dhaka city.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black" />
  <img src="https://img.shields.io/badge/Leaflet.js-Maps-199900?logo=leaflet&logoColor=white" />
  <img src="https://img.shields.io/badge/License-MIT-green" />
</p>

---

## 📖 About

**OneTap Bus** is a simple and reliable bus search and fare information platform designed for daily commuters in **Dhaka, Bangladesh**. It helps users quickly find available bus routes, bus names, and estimated fares between two locations — without any confusion.

Built with local routes and real commuting needs in mind, OneTap Bus aims to make public transport information easier to access, faster to understand, and practical for everyday travel across Dhaka.

---

## ✨ Features

### 🔍 Route Search & Fare Calculation
- Search buses by **source** and **destination** stops
- Dynamic fare calculation based on distance between stops
- **Student fare** discount option for eligible passengers
- View the full route with all intermediate stops

### 🚌 Bus Profiles
- Detailed bus profile pages with **image gallery**
- Bus type badges (**AC / Non-AC**)
- Company information and route visualization
- Interactive **route map** powered by Leaflet.js with OpenStreetMap tiles

### 🎟️ Seat Reservation System
- **Visual seat layout** with real-time availability
- Interactive seat selection (Available → Selected → Booked)
- Date-based booking to prevent double reservations
- Driver seat indicator for realistic bus layout

### 💳 Payment Gateway (Simulated)
- **Credit/Debit Card** payment form
- **Mobile Banking** support (bKash, Nagad)
- Animated payment processing with loading spinner
- Secure booking confirmation flow

### 🎫 E-Ticket Generation
- Digital e-ticket with full journey details
- **QR code** for ticket verification
- **PDF download** powered by html2pdf.js
- Passenger name, seat numbers, travel date, and route info

### ⭐ Ratings & Reviews
- Star rating system (1–5 stars)
- Written reviews with user attribution
- Average rating display on bus profiles
- Admin moderation (comment visibility toggle & deletion)

### 👤 User Authentication
- User registration with **role-based access** (Passenger / Admin)
- Secure admin registration with **authentication key**
- Session-based login/logout
- Profile management with avatar upload

### 🛡️ Admin Panel
- Manage buses and routes
- View and moderate user reviews
- Toggle comment visibility
- Delete inappropriate comments

### 🌐 Bilingual Support
- Full **English** and **বাংলা (Bengali)** language support
- Session-based language switching
- All UI strings internationalized via `lang.php`

### 📱 Responsive Design
- Mobile-friendly layout with CSS media queries
- Dark-themed modern UI with glassmorphism effects
- Parallax hero sections with mouse-tracking animations
- Smooth scrolling and micro-interactions

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Backend** | PHP 8.x |
| **Database** | MySQL / MariaDB |
| **Maps** | Leaflet.js + OpenStreetMap |
| **PDF Generation** | html2pdf.js |
| **QR Codes** | QR Server API |
| **Server** | Apache (XAMPP) |

---

## 🗄️ Database Schema

The application uses a relational MySQL database (`bus_fare`) with the following tables:

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    users     │     │     bus      │     │ bus_company   │
├──────────────┤     ├──────────────┤     ├──────────────┤
│ id (PK)      │     │ bus_id (PK)  │────▶│ company_id   │
│ name         │     │ name         │     │ name         │
│ email        │     │ type (ac/..) │     └──────────────┘
│ password     │     │ company_id   │
│ role         │     └──────┬───────┘
└──────┬───────┘            │
       │              ┌─────┴────────┐
       │              │    route     │
       │              ├──────────────┤
       │              │ route_id(PK) │
       │              │ bus_id (FK)  │
       │              └──────┬───────┘
       │                     │
       │              ┌──────┴───────┐     ┌──────────────┐
       │              │ route_stop   │────▶│    stop      │
       │              ├──────────────┤     ├──────────────┤
       │              │ route_id(FK) │     │ stop_id (PK) │
       │              │ stop_id (FK) │     │ stop_name    │
       │              │ stop_order   │     │ latitude     │
       │              └──────────────┘     │ longitude    │
       │                                   └──────────────┘
       │
  ┌────┴─────────┐     ┌──────────────┐
  │  bookings    │     │ bus_rating   │
  ├──────────────┤     ├──────────────┤
  │ id (PK)      │     │ id (PK)      │
  │ user_id (FK) │     │ user_id (FK) │
  │ bus_id (FK)  │     │ bus_id (FK)  │
  │ seat_number  │     │ rating       │
  │ booking_date │     │ comment      │
  │ source_id    │     │ created_at   │
  │ dest_id      │     └──────────────┘
  └──────────────┘

  ┌──────────────┐
  │ fare_policy  │
  ├──────────────┤
  │ id (PK)      │
  │ base_fare    │
  │ per_stop     │
  │ student_disc │
  └──────────────┘
```

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)
- PHP 8.0 or higher
- MySQL / MariaDB
- A modern web browser

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/akiibot/OneTap-Bus.git
   ```

2. **Move to your web server directory**
   ```bash
   # Move or clone directly into htdocs
   mv OneTap-Bus /path/to/xampp/htdocs/OneTap-Bus
   ```

3. **Create the database**
   ```sql
   CREATE DATABASE bus_fare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Import the schema**
   ```bash
   mysql -u root -p bus_fare < schema_dump.sql
   ```

5. **Configure database connection**  
   Edit `db.php` if your MySQL credentials differ from the defaults:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'bus_fare');
   ```

6. **Start XAMPP** (Apache + MySQL)

7. **Visit the app**
   ```
   http://localhost/OneTap-Bus/
   ```

---

## 📁 Project Structure

```
OneTap-Bus/
├── admin/                  # Admin panel pages
│   ├── buses.php           # Bus management
│   ├── dashboard.php       # Admin dashboard
│   ├── header.php          # Admin header/nav
│   └── routes.php          # Route management
├── assets/
│   ├── buses/              # Bus images (per bus ID)
│   ├── images/             # Site images (hero, team, icons)
│   └── default-avatar.png  # Default user avatar
├── db.php                  # Database connection config
├── lang.php                # Bilingual language strings (EN/BN)
├── header.php              # Global header & navigation
├── footer.php              # Global footer
├── index.php               # Homepage (search + bus list)
├── result.php              # Search results page
├── bus.php                 # Bus profile (gallery, map, seats, reviews)
├── payment.php             # Payment gateway (card + mobile banking)
├── book_seat.php           # Booking API endpoint
├── ticket.php              # E-ticket display + PDF download
├── my_bookings.php         # User booking history
├── signin.php              # Login page
├── sign-up.php             # Registration page
├── about.php               # About page with team section
├── rate_bus.php             # Review submission handler
├── fetch_seats.php          # Seat availability API
├── schema_dump.sql          # Full database schema
├── style.css               # Main stylesheet
├── auth.css                # Authentication pages styles
└── parallax.js             # Parallax effect script
```

---

## 👥 Team

| Member | Role |
|--------|------|
| **Yeanul Haque Khan Akib** | Developer |
| **Sumaiya Islam Aritra** | Developer |
| **Tahmid Islam** | Developer |


---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

<p align="center">
  Made with ❤️ for the commuters of Dhaka 🇧🇩
</p>
