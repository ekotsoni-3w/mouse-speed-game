# Emoji Mouse Speed Game

A browser-based mini game where players move the mouse inside the game area to gain points before the countdown ends.

The project demonstrates frontend game logic, client-server communication, PHP input validation, and secure score storage with MariaDB/MySQL.

> The current scoring system counts mouse movement events rather than measuring physical cursor velocity.

## Live Demo

🎮 [Play the Emoji Mouse Speed Game](https://ekotsoni3w.alwaysdata.net)

## Features

- Countdown timer
- Dynamic scoring based on mouse movement
- Animated emoji effects
- Instructions modal
- Game-over screen
- Score persistence in MariaDB/MySQL
- Restart option
- JSON API responses
- Server-side score validation
- Prepared statements for secure database queries

## Technologies

- HTML5
- CSS3
- JavaScript
- Fetch API
- PHP
- MariaDB/MySQL
- Git and GitHub

## Project Structure

```text
mouse-speed-game/
├── index.php
├── style.css
├── save_score.php
├── database.sql
├── config.example.php
├── .gitignore
├── CHANGELOG.md
├── LICENSE
└── README.md
```

The local `config.php` file is intentionally excluded from Git because it contains database credentials.

## Requirements

- PHP 8.2 or later
- MariaDB 10.4 or later, or a compatible MySQL version
- PHP `mysqli` extension
- A modern web browser

## Local Setup

### 1. Clone the repository

```bash
git clone https://github.com/ekotsoni-3w/mouse-speed-game.git
cd mouse-speed-game
```

### 2. Create the database

Make sure MariaDB/MySQL is running, then import:

```bash
mysql -u root < database.sql
```

When using XAMPP on macOS, the command may be:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root < database.sql
```

The script creates the `game_db` database and the `scores` table.

### 3. Create a restricted database user

Connect as a database administrator and run:

```sql
CREATE USER 'mouse_game_app'@'127.0.0.1'
IDENTIFIED BY 'replace_with_a_secure_password';

GRANT SELECT, INSERT
ON game_db.scores
TO 'mouse_game_app'@'127.0.0.1';
```

This user can read and insert scores but cannot delete tables or modify the database structure.

### 4. Create the local configuration

Copy the example file:

```bash
cp config.example.php config.php
```

Open `config.php` and enter the local database username and password:

```php
<?php

return [
    "host" => "127.0.0.1",
    "username" => "mouse_game_app",
    "password" => "your_secure_password",
    "database" => "game_db",
    "port" => 3306
];
```

Never commit `config.php`. It is already included in `.gitignore`.

### 5. Start the application

```bash
php -S 127.0.0.1:8000
```

Open:

```text
http://127.0.0.1:8000
```

## Score API

The game sends scores to:

```text
POST /save_score.php
```

Expected form field:

```text
score=<integer from 0 to 100000>
```

Possible responses include:

- `201 Created` — score saved successfully
- `405 Method Not Allowed` — request was not sent with `POST`
- `422 Unprocessable Content` — score is missing or invalid
- `500 Internal Server Error` — configuration or database failure

Database errors are written to the server logs and are not exposed in the API response.

## Security Practices

- Strict server-side integer validation
- Prepared statements
- Restricted database permissions
- Local credentials excluded from Git
- Generic client-facing database errors
- Unicode-compatible database connection

## Roadmap

- [ ] Add player names and a leaderboard
- [ ] Improve Start, Stop, and Game Over state handling
- [ ] Handle API errors in the interface
- [ ] Separate JavaScript from `index.php`
- [ ] Improve mobile and keyboard accessibility
- [ ] Add automated tests
- [ ] Add screenshots and a live demo

## License

This project is available under the [MIT License](LICENSE).
