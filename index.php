<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR App</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1> PALCOA SOLUTIONS</h1>
    <h1>OCR App</h1>

    <div class="converter-box">
        <form id="uploadForm" enctype="multipart/form-data" method="post" action="phtry.php">
            <input type="file" name="file" accept="image/*" required>
            <button type="submit">Upload</button>
        </form>
    </div>
    <div id="result">
        <?php
            if(isset($_GET['result'])) {
                echo "OCR Result: " . $_GET['result'];
            }
        ?>
    </div>
</body>
</html>
