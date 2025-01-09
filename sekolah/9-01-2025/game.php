<?php
include 'db/config.php';
session_start();

// Memilih pertanyaan secara acak jika belum ada di sesi
if (!isset($_SESSION['current_question'])) {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['current_question'] = $result->fetch_assoc();
    }
}

$question = $_SESSION['current_question'];

$message = '';
$isCorrect = null;

// Inisialisasi rekaman permainan
if (!isset($_SESSION['record'])) {
    $_SESSION['record'] = ['correct' => 0, 'wrong' => 0];
}

// Proses jawaban
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($question) {
        $answer = trim($_POST['answer']);
        $correctAnswer = trim($question['answer']);

        if (strcasecmp($answer, $correctAnswer) === 0) {
            $isCorrect = true;
            $message = "Jawaban benar!";
            $_SESSION['record']['correct']++;
        } else {
            $isCorrect = false;
            $message = "Jawaban salah!";
            $_SESSION['record']['wrong']++;
        }

        // Reset pertanyaan untuk soal berikutnya
        unset($_SESSION['current_question']);
        header("Refresh: 2");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tebak Daerah di Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        header {
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.8);
            width: 100%;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        header button {
            border: none;
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .record-button {
            background-color: #ffc107;
            color: #333;
        }

        .record-button:hover {
            background-color: #e0a800;
        }

        .music-toggle {
            background-color: #28a745;
            color: white;
        }

        .music-toggle:hover {
            background-color: #218838;
        }

        main {
            text-align: center;
            padding: 20px;
            flex: 1;
        }

        h1 {
            margin-bottom: 20px;
            color: #444;
        }

        .feedback {
            font-size: 1.5rem;
            margin: 20px 0;
        }

        .correct {
            color: green;
        }

        .wrong {
            color: red;
        }

        .question-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 600px;
        }

        .question-container img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: calc(100% - 20px);
            max-width: 300px;
            margin: 10px auto;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        footer {
            margin-top: auto;
            padding: 10px 20px;
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            width: 100%;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <header>
        <button class="record-button" onclick="showRecord()">Record Permainan</button>
        <button id="toggleMusic" class="music-toggle">Play Music</button>
    </header>

    <main>
        <h1>Tebak Daerah di Indonesia</h1>

        <?php if (!empty($message)): ?>
            <div class="feedback <?php echo $isCorrect === true ? 'correct' : 'wrong'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($question): ?>
            <div class="question-container">
                <?php if (!empty($question['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($question['image_url']); ?>" alt="Gambar Soal">
                <?php endif; ?>
                <p><strong>Pertanyaan:</strong> <?php echo htmlspecialchars($question['question']); ?></p>
                <p><strong>Petunjuk:</strong> <?php echo htmlspecialchars($question['hint']); ?></p>
            </div>

            <form method="post">
                <label for="answer">Jawaban:</label>
                <input type="text" id="answer" name="answer" required>
                <button type="submit">Submit</button>
            </form>
        <?php else: ?>
            <p>Tidak ada pertanyaan yang tersedia. Silakan tambahkan data ke tabel <strong>questions</strong> di database.</p>
        <?php endif; ?>
    </main>

    <footer>
        &copy; 2025 Game Teka-Teki
    </footer>

    <audio id="correctSound">
        <source src="sounds/correct.mp3" type="audio/mpeg">
        Browser Anda tidak mendukung audio tag.
    </audio>
    <audio id="wrongSound">
        <source src="sounds/wrong.mp3" type="audio/mpeg">
        Browser Anda tidak mendukung audio tag.
    </audio>
    <audio id="backgroundMusic" loop>
        <source src="sounds/background.mp3" type="audio/mpeg">
        Browser Anda tidak mendukung audio tag.
    </audio>

    <script>
        const isCorrect = <?php echo json_encode($isCorrect); ?>;

        // Memainkan suara jawaban benar/salah
        window.onload = function() {
            if (isCorrect === true) {
                document.getElementById('correctSound').play();
            } else if (isCorrect === false) {
                document.getElementById('wrongSound').play();
            }
        };

        // Kontrol musik latar
        const music = document.getElementById('backgroundMusic');
        const toggleMusic = document.getElementById('toggleMusic');

        toggleMusic.addEventListener('click', () => {
            if (music.paused) {
                music.play();
                toggleMusic.textContent = "Mute Music";
                toggleMusic.style.backgroundColor = "red";
            } else {
                music.pause();
                toggleMusic.textContent = "Play Music";
                toggleMusic.style.backgroundColor = "#28a745";
            }
        });

        // Tampilkan rekaman permainan
        function showRecord() {
            const record = <?php echo json_encode($_SESSION['record']); ?>;
            alert(`Rekaman Permainan:\nBenar: ${record.correct}\nSalah: ${record.wrong}`);
        }
    </script>
</body>
</html>
