<?php
session_start();

// Periksa apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: game.php"); // Jika sudah login, arahkan ke halaman game
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adventure Game</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS untuk mempercantik tampilan */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #1a1a1d, #4e4e50);
            color: #fff;
            text-align: center;
        }

        header {
            padding: 50px 20px;
            background: #1a1a1d;
            color: #fff;
        }

        header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        header p {
            font-size: 1.2rem;
            margin: 0;
        }

        nav {
            background: #c3073f;
            padding: 15px 0;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.2rem;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #f2a365;
        }

        main {
            padding: 20px;
        }

        main img {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            max-width: 90%;
            height: auto;
            margin: 20px 0;
        }

        main p {
            font-size: 1.2rem;
            margin: 20px 0;
            line-height: 1.5;
            color:#1a1a1d;
        }

        footer {
            background: #1a1a1d;
            padding: 15px 0;
            font-size: 0.9rem;
        }

        footer p {
            margin: 0;
            color: #ccc;
        }

        .btn {
            display: inline-block;
            background: #c3073f;
            color: #fff;
            padding: 10px 20px;
            margin: 10px;
            font-size: 1rem;
            text-transform: uppercase;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background: #f2a365;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Adventure Game</h1>
        <p>Embark on a journey to explore, fight enemies, and collect treasures!</p>
    </header>

    <nav>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </nav>

    <main>
        <img src="cover.jpg" alt="Adventure World">
        <p>
        Mulailah petualangan Anda hari ini! Buat akun atau masuk untuk melanjutkan perjalanan Anda.
        </p>
        <a href="register.php" class="btn">Register</a>
        <a href="login.php" class="btn">Login</a>
    </main>

    <footer>
        <p>&copy; 2025 Adventure Game @Xsanyyyy.</p>
    </footer>
</body>
</html>
