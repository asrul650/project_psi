<?php
// Halaman Matchup - Embed React App
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchup</title>
    <style>
        body { margin: 0; padding: 0; }
        .iframe-container {
            width: 100vw;
            height: 100vh;
            border: none;
            overflow: hidden;
        }
        iframe {
            width: 100vw;
            height: 100vh;
            border: none;
        }
    </style>
</head>
<body>
    <div class="iframe-container">
        <iframe src="http://localhost:5175/heroverse/fitur%20baru/dist/" frameborder="0" allowfullscreen></iframe>
    </div>
</body>
</html> 