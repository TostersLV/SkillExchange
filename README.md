# SkillExchange

SkillExchange is a web platform where people trade skills instead of money: an hour of your skill for an hour of someone else's. For example, you teach programming basics and get English lessons in return.

## What you can do

- **Post an exchange** – say which skill you offer and which skill you want in return, and pick a category.
- **Explore** – search posts by what you want to learn or what you can teach, filter by category and sort by best match, newest or highest rated. Posts that want a skill you offer are marked as a great match.
- **Propose a swap** – send an offer with a message on someone's post.
- **Accept or decline offers** – the author can accept one or several offers and talk them through before choosing.
- **Chat** – the two members of an exchange get a private chat to agree on the details.
- **Complete or cancel** – an exchange is completed when both members confirm it, and cancelled when both agree to cancel.
- **Rate each other** – after a completed exchange both members rate each other from 1 to 5. A member's rating is the average of the ratings they received and is shown on their profile.

## Built with

- [Laravel 13](https://laravel.com) (PHP)
- [Livewire 4](https://livewire.laravel.com) and [Flux UI](https://fluxui.dev)
- [Laravel Fortify](https://laravel.com/docs/fortify) for registration, login and password reset
- [Tailwind CSS 4](https://tailwindcss.com) and [Vite](https://vite.dev)
- [Pest](https://pestphp.com) for tests

## Requirements

- PHP 8.3 or newer
- [Composer](https://getcomposer.org)
- [Node.js](https://nodejs.org) 22 or newer, with npm
- A database: SQLite (no setup needed) or MySQL

## Installation

1. **Get the code and go into the project folder**

   ```bash
   git clone https://github.com/TostersLV/SkillExchange.git
   cd SkillExchange
   ```

2. **Install the PHP dependencies**

   ```bash
   composer install
   ```

3. **Create the environment file and the application key**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   On Windows Command Prompt use `copy .env.example .env` instead of `cp`.

4. **Choose a database**

   - **SQLite (default):** nothing to change. When asked during the next step whether to create the database file, answer *yes*.
   - **MySQL:** create an empty database (for example `skillexchange`) and set these lines in `.env`:

     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=skillexchange
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **Create the tables and fill them with starting data**

   ```bash
   php artisan migrate --seed
   ```

   The `--seed` part is required: it creates the skill categories (a post can't be created without one) plus a test account and example posts.

6. **Link the storage folder**

   ```bash
   php artisan storage:link
   ```

   Uploaded profile pictures are saved in `storage/app/public`. This makes them reachable from the browser under `/storage`; without it every profile picture shows as a broken image.

7. **Install and build the frontend**

   ```bash
   npm install
   npm run build
   ```

8. **Start the app**

   ```bash
   composer run dev
   ```

   Then open the address shown in the terminal (usually <http://localhost:8000>).

## Environment settings

All settings live in `.env`. The defaults from `.env.example` work for local development; these are the ones you may need to change:

| Setting | What it does |
| --- | --- |
| `APP_URL` | The address you open the app on, for example `http://localhost:8000`. Profile picture links are built from it, so it must match exactly (including the port), or pictures won't load. |
| `APP_ENV` / `APP_DEBUG` | `local` and `true` for development. On a live server use `production` and `false`, so error pages don't show code or settings. |
| `DB_CONNECTION` and `DB_*` | Which database to use. See step 4 above. |
| `MAIL_*` | Used for password reset emails. With the default `MAIL_MAILER=log` no email is sent; the message is written to `storage/logs/laravel.log` instead. Set real SMTP details to send emails. |

After changing `.env` on a server where the config is cached, run `php artisan config:clear` (or `config:cache` again) so the change takes effect.

## Deployment

To run SkillExchange on a live server:

1. Get the code onto the server and create `.env` from `.env.example`, with:
   - `APP_ENV=production` and `APP_DEBUG=false`
   - `APP_URL` set to the site's real address, for example `https://skillexchange.example.com`
   - your production database and mail settings
2. Point the web server (Nginx or Apache) at the `public` folder, never the project root.
3. Run:

   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --class=CategorySeeder --force
   php artisan storage:link
   npm ci
   npm run build
   php artisan optimize
   ```

   Run `key:generate` only on the first deployment; generating a new key later logs everyone out. Seed only the categories: the full `--seed` also creates the test account and example posts, which don't belong on a live site.
4. Make sure the web server can write to `storage` and `bootstrap/cache`.

For later updates, pull the new code and run the same commands again, except `key:generate`.

## Test account

After seeding you can log in with:

| Email | Password |
| --- | --- |
| test@example.com | password |

You can also register a new account from the login page.

## Running the tests

```bash
composer test
```

This checks the code style (Pint), runs static analysis (PHPStan) and runs the Pest test suite – the same checks that run on GitHub for every push.

## Starting over

To delete all data and recreate the database with fresh starting data:

```bash
php artisan migrate:fresh --seed
```
