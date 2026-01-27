## Laravel Tasks — Simple Laravel Task Manager

A minimal Laravel 10+ application to manage tasks per project with:
- Create, edit, delete tasks
- Drag-and-drop to reorder tasks; priorities auto-update (1 = top)
- Project dropdown to view tasks for a selected project
- MySQL persistence

No frontend build required — Bootstrap and SortableJS are loaded via CDN.

---

## 1) Requirements
- PHP 8.2+
- Composer 2.5+
- MySQL 8+

## 2) Install
From the project root:
```bash
cp .env.example .env
php artisan key:generate
```

Create a MySQL database (e.g. `test_laravel`) and update `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_laravel
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

Run migrations:
```bash
php artisan migrate
```

## 3) Run locally
```bash
php artisan serve
```
Open http://127.0.0.1:8000 in your browser.

## 4) How to use
- Go to “Projects” to add a project.
- On the Tasks page, select a project from the dropdown.
- Add tasks using the form on the right.
- Drag tasks to reorder; priorities update automatically.
- Use inline “Save” to edit a task name; “Delete” removes it.

## 5) Deploying
- Set correct environment variables in `.env` on the server.
- Run:
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
- Point your web server (Apache/Nginx) to `public/`.
- Ensure `storage/` and `bootstrap/cache/` are writable by the web user.

## Notes
- This app uses standard Laravel MVC, Eloquent relationships, Form Requests for validation, resourceful routes, and Blade views.
- Frontend drag-and-drop uses SortableJS; reorder is persisted via a dedicated endpoint and priorities are normalized server-side.

