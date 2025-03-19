<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION['submitted'])) {
    $productName = $conn->real_escape_string($_POST["product_name"]);
    $productDescription = $conn->real_escape_string($_POST["product_description"]);
    $productPrice = floatval($_POST["product_price"]);
    $productStock = intval($_POST["product_stock"]);

    // Handling image file
    $imageDir = "./uploads/images/";
    $imageFile = $imageDir . basename($_FILES["product_image"]["name"]);
    $imageFileName = basename($_FILES["product_image"]["name"]);
    $imageFileType = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));

    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
        echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
    } else {
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if ($check !== false) {
            if (!is_dir($imageDir)) {
                mkdir($imageDir, 0777, true);
            }
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $imageFile)) {
                $imageUploaded = true;
            } else {
                echo "Sorry, there was an error uploading your image file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Handling PDF file
    $pdfDir = "./uploads/pdf/";
    $pdfFile = $pdfDir . basename($_FILES["product_pdf"]["name"]);
    $pdfFileName = basename($_FILES["product_pdf"]["name"]);
    $pdfFileType = strtolower(pathinfo($pdfFile, PATHINFO_EXTENSION));

    if ($pdfFileType != "pdf") {
        echo "Sorry, only PDF files are allowed.";
    } else {
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        if (move_uploaded_file($_FILES["product_pdf"]["tmp_name"], $pdfFile)) {
            $pdfUploaded = true;
        } else {
            echo "Sorry, there was an error uploading your PDF file.";
        }
    }

    if ($imageUploaded && $pdfUploaded) {
        $sql = "INSERT INTO products (name, description, price, stok, image, pdf_file) VALUES ('$productName', '$productDescription', '$productPrice', '$productStock', '$imageFileName', '$pdfFileName')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['submitted'] = true;
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    unset($_SESSION['submitted']);
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" href="style admin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* style admin.css */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        nav {
            background-color: #6d9ee7;
            padding: 10px 0;
        }
        
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        
        nav ul li {
            display: inline;
            margin: 0 10px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        
        nav ul li a:hover {
            text-decoration: underline;
        }
        
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            text-align: center;
            color: #6d9ee7;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .form-group textarea {
            height: 100px;
        }
        
        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #6d9ee7;
            border: none;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        
        .form-group input[type="submit"]:hover {
            background-color: #5b8bd4;
        }
        
        img#image_preview {
            display: block;
            margin-top: 10px;
            max-width: 100%;
        }
        
        @media (max-width: 600px) {
            .container {
                width: 95%;
            }
        
            nav ul li {
                display: block;
                margin: 10px 0;
            }
        }
        .popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.8);
    background: white;
    padding: 20px;
    box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    text-align: center;
    z-index: 1000;
    opacity: 0;
    transition: all 0.3s ease-out;
}

.popup.show {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(8px); /* Efek blur */
    background: rgba(0, 0, 0, 0.3);
    z-index: 999;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.overlay.show {
    display: block;
    opacity: 1;
}

.popup button {
    margin-top: 10px;
    padding: 10px 15px;
    background: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-weight: bold;
    transition: background 0.3s ease;
}

.popup button:hover {
    background: #45a049;
}

    </style>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('image_preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let form = document.querySelector("form");
        let popup = document.getElementById("popup");
        let overlay = document.getElementById("overlay");
        let closeBtn = document.getElementById("closePopup");

        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Cegah submit langsung
            popup.classList.add("show");
            overlay.classList.add("show");

            closeBtn.addEventListener("click", function () {
                popup.classList.remove("show");
                overlay.classList.remove("show");

                // Delay agar efek keluar selesai sebelum submit
                setTimeout(() => {
                    form.submit();
                }, 300);
            });
        });
    });
</script>


</head>
<body>
   
    <div class="container">
        <h2>Add product</h2>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            <div class="form-group">
                <label for="product_description">Product Description:</label>
                <textarea id="product_description" name="product_description" required></textarea>
            </div>
            <div class="form-group">
                <label for="product_price">Product Price:</label>
                <input type="number" id="product_price" name="product_price" required>
            </div>
            <div class="form-group">
                <label for="product_image">Product Image:</label>
                <input type="file" id="product_image" name="product_image" required onchange="previewImage(event)">
                <img id="image_preview" src="#" alt="Image Preview" style="display: none;">
            </div>
            <div class="form-group">
                <label for="product_pdf">Product PDF:</label>
                <input type="file" id="product_pdf" name="product_pdf" required>
            </div>
            <input type="submit" value="Upload Product">
        </form>
    </div>
    <div class="overlay" id="overlay"></div>
    <div class="overlay" id="overlay"></div>
<div class="popup" id="popup">
    <p><strong>Produk berhasil diunggah!</strong></p>
    <button id="closePopup">OK</button>
</div>


</body>
</html>
