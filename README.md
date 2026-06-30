# Bayani Chronicles API

Backend API for **Bayani Chronicles**, an educational role-playing game developed as a **team capstone project** using **Laravel** and **Unity**.

---

## 👨‍💻 My Role

This was a collaborative capstone project. My primary responsibility was the **backend development** of the system, including:

- REST API Development
- Authentication & Authorization
- Database Design and Management
- Business Logic Implementation
- Server-side Validation
- Unity API Integration
- Backend Security and Data Management

The Unity game client, user interface, and game assets were developed collaboratively by the team.

---

## 📖 Project Overview

Bayani Chronicles follows a **client-server architecture**.

The **Unity client** is responsible for gameplay, rendering, animations, and user interactions, while the **Laravel backend** manages authentication, business logic, validation, and persistent game data through REST APIs.

This separation allows the backend to centralize sensitive operations while keeping the client focused on gameplay.

---

## ✨ Features

- User Authentication
- Player Profile Management
- Character Management
- Inventory System
- Quest Management
- Secure REST API
- Server-side Validation
- Role-Based Access Control

---

## 🏗️ System Architecture

```text
                Bayani Chronicles

              Unity Game Client
                     │
                REST API
                     │
                     ▼
             Laravel Backend API
                     │
              Business Logic
                     │
                     ▼
               MySQL Database
```

The Unity client never communicates directly with the database. All requests are validated and processed by the Laravel backend before data is stored or returned.

---

## 🛠️ Technology Stack

### Backend

- Laravel
- PHP
- MySQL
- RESTful API

### Client

- Unity
- C#

---

## 🚀 Installation

Clone the repository

```bash
git clone https://github.com/Ikong217/bayani-chronicles-api.git
```

Install dependencies

```bash
composer install
```

Create the environment file

```bash
cp .env.example .env
```

Generate the application key

```bash
php artisan key:generate
```

Configure your database inside `.env`

Run migrations

```bash
php artisan migrate
```

Start the development server

```bash
php artisan serve
```

---

## 📂 Related Repository

**Unity Client**

https://github.com/Ikong217/bayani-chronicles-client

<!-- ---

## 📸 Screenshots

Screenshots will be added soon. -->

---

## 📄 License

This project is intended for educational and portfolio purposes.
