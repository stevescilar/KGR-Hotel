# 🌿 Kitonga Garden Resort — Laravel Hotel Management System

A full-featured Hotel Management Platform built with **Laravel 11**, **Livewire**, **Alpine.js**, and **Tailwind CSS**.

## 🏗️ System Modules

| Module | Description |
|---|---|
| 🛏️ Room Booking | Real-time availability, pricing engine, M-Pesa & card payments |
| 🍽️ Food & Drinks | Menu management, table reservations, room service orders |
| 🎉 Events & Weddings | Package listings, inquiry forms, deposit payments, scheduling |
| 🎟️ Gate Ticketing | Online ticket purchase, QR code generation & scanning |
| 💎 Loyalty Program | Points earning, tier levels, redemption at checkout |
| 🎁 Gift Cards | Digital gift card purchase, send & redeem |
| 👔 HR / Careers | Job listings, online applications, CV uploads, tracking |
| 📊 Admin Dashboard | PMS dashboard, analytics, staff roles, content management |

## 🛠️ Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Livewire 3 + Alpine.js + Blade
- **Styling**: Tailwind CSS
- **Database**: MySQL
- **Payments**: M-Pesa Daraja API + Flutterwave
- **SMS/Email**: Africa's Talking + Laravel Mail
- **QR Codes**: `simplesoftwareio/simple-qrcode`
- **Auth**: Spatie Laravel Permission (role-based)

## 🚀 Installation

```bash
git clone https://github.com/yourorg/kgr-system.git
cd kgr-system

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configure your .env (DB, mail, M-Pesa, etc.)
php artisan migrate --seed

npm run dev
php artisan serve
```

## 👥 Default Roles & Permissions

| Role | Access |
|---|---|
| `super_admin` | Full system access |
| `manager` | All modules except system settings |
| `receptionist` | Bookings, check-in/out, gate tickets |
| `fnb_staff` | Restaurant orders, menu viewing |
| `housekeeper` | Room status updates |
| `hr_admin` | Careers and applications |

## 📁 Project Structure

```
app/
  Models/          # Eloquent models
  Http/
    Controllers/
      Admin/       # Admin panel controllers
      Api/         # API endpoints (mobile)
    Middleware/    # Auth, role-checking, etc.
  Services/        # Business logic (BookingService, PaymentService, etc.)
  Enums/           # PHP 8.1 Enums (RoomStatus, BookingStatus, etc.)

database/
  migrations/      # All table migrations
  seeders/         # Demo data seeders
  factories/       # Model factories for testing

resources/
  views/
    layouts/       # Base layout files
    admin/         # Admin dashboard views
    public/        # Guest-facing views
    emails/        # Email templates

routes/
  web.php          # Public & auth routes
  admin.php        # Admin panel routes
  api.php          # API routes
```
