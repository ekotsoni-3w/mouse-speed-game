# Emoji Mouse Speed Game

A simple web-based mini game where the player moves the mouse inside the game area to gain points before the countdown ends.

This project was built for learning purposes, practicing:

- DOM manipulation
- Timers & Events
- CSS animations & blur effects
- PHP & MySQL integration
- Fetch API communication

---

## How It Works

1. The player reads the instructions.
2. Presses **Start**.
3. Moves the mouse inside the game area.
4. Gains points for every movement.
5. The game ends when the countdown reaches zero.
6. Final score is displayed in a Game Over modal.
7. Score is saved to the database.

---

## Features

-  Countdown Timer  
-  Dynamic Score System  
-  Animated Emoji Effects  
-  Instructions Modal  
-  Game Over Screen with Blur  
-  Score saved to MySQL Database  
-  Restart Option  

---

## Technologies Used

- HTML5  
- CSS3 (Animations & Glass Blur Effects)  
- JavaScript (Events, setInterval, Fetch API)  
- PHP  
- MySQL  
- XAMPP (Local Development)

---

## Project Structure

```
htdocs/
 └── mouse-speed-game/
      ├── index.php
      ├── style.css
      ├── save_score.php
      └── README.md
```

---

##  Installation (Local Setup)

1. Install XAMPP
2. Place the project folder inside:

```
htdocs/
```

3. Start Apache & MySQL
4. Open browser:

```
http://localhost/emoji-mouse-speed-game
```

---

##  Database Setup

Open phpMyAdmin and run:

```sql
CREATE DATABASE game_db;

USE game_db;

CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    score INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Learning Goals

This project helped practice:

- Frontend & Backend communication
- Managing game state
- UI/UX with modals
- Basic database interaction
- Clean project structure for GitHub

---

## 📄 License

This project is open-source and free to use for educational purposes.
