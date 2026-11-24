
# 🏆 Tourny Mate
### Your Ultimate Tournament Management Platform

**Tourny Mate** is a web-based tournament management platform developed as a Database Management System (DBMS) project. It provides a centralized platform for managing tournaments, matches, players, teams, scores, and sports-related updates.

---

## ✨ Key Features

- **User Authentication** – Registration, login, logout, and profile management.
- **Tournament Management** – Browse tournaments and view tournament details.
- **Match Management** – Access match information and updates.
- **Live Scores** – Display match scores.
- **Player Management** – Explore player profiles and team information.
- **Score Management** – View match scores and tournament results.
- **Sports News & Highlights** – Browse sports news and match highlights.
- **Admin Dashboard** – Dedicated administration panel.
- **Notifications** – User notification functionality.
- **Contact System** – Communication functionality.

## 🛠️ Technology Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **UI Framework:** Bootstrap
- **Real-Time Communication:** WebSocket

## 📂 Project Structure

```text
Tourny_mate/
│
├── Admin/
├── components/
│   └── shared/
├── css/
├── dashboard/
│   └── user/
├── db/
├── fonts/
│
├── index.php
├── login.php
├── register.php
├── profile.php
├── logout.php
│
├── allTournaments.php
├── view_tournament.php
├── match_details.php
├── allScores.php
├── score.php
│
├── players.php
├── player_details.php
├── news.php
├── highlights.php
│
├── get_notifications.php
├── websocket.php
├── websocket_sender.php
├── config.php
└── README.md
```

## ⚙️ Installation & Setup

### Prerequisites

- PHP 8.0+
- MySQL
- XAMPP or another Apache server
- Web Browser

### 1. Clone the Repository

```bash
git clone https://github.com/afiatasnimria/Tourny_mate.git
```

### 2. Setup Project

Move the cloned project folder into the XAMPP `htdocs` directory.

### 3. Configure Database

1. Start Apache and MySQL from XAMPP.
2. Open phpMyAdmin.
3. Create a new MySQL database.
4. Import the SQL file available in the `db/` directory.
5. Update database credentials in `config.php`.

### 4. Run the Application

Open your browser and visit:

```text
http://localhost/Tourny_mate/
```

## 🎯 Project Objectives

- Apply database management concepts in a real-world application.
- Manage tournament, match, player, and score information.
- Implement user authentication and role-based access.
- Provide a centralized sports management platform.
- Explore real-time communication using WebSocket.

## 🚀 Future Improvements

- Automated tournament bracket generation.
- Advanced team registration and management.
- Enhanced real-time score synchronization.
- Tournament analytics and statistics.
- Improved mobile responsiveness.

## 👩‍💻 Developer

**Afia Tasnim Ria**

- GitHub: [afiatasnimria](https://github.com/afiatasnimria)
- Repository: [Tourny Mate](https://github.com/afiatasnimria/Tourny_mate)

---

<p align="center">
  <b>Tourny Mate — Making Tournament Management Simpler.</b>
</p>
```
