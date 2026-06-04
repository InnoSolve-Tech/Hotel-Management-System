<p align="center">
  <img src="public/uploads/logo.jpg" alt="Hot-L logo" width="140">
</p>

# Hot-L

Hot-L is a hotel management system built for teams that need a cleaner back office for daily operations, bookings, guests, rooms, finance, and administration.

It is powered by Laravel and now includes permission-aware navigation, database-backed roles, and a more polished admin experience.

## Highlights

- Manage rooms, guests, bookings, employees, banks, invoices, payments, and ledgers from one admin panel
- Use real role-based access control with roles, permissions, and protected web/API access
- Show a compact permission-aware sidebar based on what each user can actually access
- Create and edit custom roles from the `Roles & Permissions` page
- Run a branded Hot-L experience across login, dashboard, and admin surfaces

## Default Roles

Hot-L ships with these default system roles:

- `SuperAdmin`
- `Admin`
- `Manager`
- `Cashier`
- `Staff`

Permissions are stored in the database and can be updated from the `Roles & Permissions` page.

## Tech Stack

- `PHP 8+`
- `Laravel 9`
- `MySQL`
- `Laravel Sanctum`
- `Laravel Mix`
- `Bootstrap`, `Sass`, `jQuery`
- `AG Grid`

## Getting Started

### Requirements

Make sure you have these installed:

- `PHP 8.0+`
- `Composer`
- `Node.js` and `npm`
- `MySQL`

### Installation

```bash
git clone <your-repo-url>
cd hotelio

composer install
npm install

cp .env.example .env
php artisan key:generate
```

### Configure Environment

Update your database settings in `.env`.

Important defaults in `.env.example`:

```env
APP_NAME=Hot-L
APP_URL=http://localhost

ADMIN_EMAIL=admin@hot-l.test
ADMIN_PASSWORD=password
```

### Run Migrations and Seed Data

```bash
php artisan migrate --seed
```

This creates the RBAC tables, seeds the default permissions and roles, and creates the default admin user.

### Build Frontend Assets

For development:

```bash
npm run dev
```

For production assets:

```bash
npm run prod
```

### Start the App

```bash
php artisan serve
```

Then open:

- `http://127.0.0.1:8000`

## Default Login

After seeding, you can sign in with:

- Email: `admin@hot-l.test`
- Password: `password`

You can override these with `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` before running the seeder.

## Roles & Permissions

Hot-L now uses database-backed RBAC:

- `users.role_id` is the source of truth
- legacy `users.Role` is kept in sync for compatibility
- web routes are permission-guarded
- `api/v1/*` routes are protected with `auth:sanctum` and permission checks

Permissions follow a simple action model per module:

- `view`
- `create`
- `edit`
- `delete`

## Developer Commands

### Run Tests

```bash
php artisan test
```

### Rebuild Production Assets

```bash
npm run prod
```

### Useful Local Workflow

```bash
php artisan migrate --seed
php artisan serve
npm run dev
```

## Project Areas

Main product areas include:

- Dashboard
- Operations
- Finance
- Administration

## Contributing

Contributions are welcome. If you want to improve Hot-L, please open an issue or submit a pull request.

## Security

If you discover a security issue, please email `mail4mjaman@gmail.com`.

## License

Hot-L is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
