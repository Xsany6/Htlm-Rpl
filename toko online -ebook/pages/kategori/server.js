const express = require('express');
const multer = require('multer');
const fs = require('fs');
const path = require('path');

const app = express();
const port = 3000;

// Konfigurasi multer untuk upload file
const upload = multer({
    dest: 'assets/',
    limits: { fileSize: 40 * 1024 * 1024 }, // 40 MB
    fileFilter: (req, file, cb) => {
        if (file.mimetype === 'application/pdf') {
            cb(null, true);
        } else {
            cb(new Error('Only PDF files are allowed.'));
        }
    }
});

// Middleware untuk melayani file statis
app.use(express.static('assets'));
app.use(express.static(__dirname));

// API untuk mengunggah file
app.post('/upload', upload.single('pdfFile'), (req, res) => {
    if (!req.file) {
        return res.status(400).send('No file uploaded.');
    }

    const filePath = path.join(__dirname, 'assets', req.file.filename);
    res.json({ fileName: req.file.filename, filePath: `/assets/${req.file.filename}` });
});

// API untuk menghapus file
app.delete('/delete/:fileName', (req, res) => {
    const fileName = req.params.fileName;
    const filePath = path.join(__dirname, 'assets', fileName);

    fs.unlink(filePath, (err) => {
        if (err) {
            return res.status(404).send('File not found.');
        }
        res.send('File deleted.');
    });
});

// Jalankan server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
