<?php
require dirname(__DIR__)  . '/vendor/autoload.php';
use Spatie\Image\Image;
Image::useImageDriver('gd');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $uploadDir = 'upload/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Pobierz parametry z formularza
    $width = isset($_POST['width']) && is_numeric($_POST['width']) ? (int)$_POST['width'] : null;
    $height = isset($_POST['height']) && is_numeric($_POST['height']) ? (int)$_POST['height'] : null;
    $quality = isset($_POST['quality']) && is_numeric($_POST['quality']) ? (int)$_POST['quality'] : 90;
    $format = $_POST['format'] ?? 'original';

    // Przetwórz obraz jeśli nie jest 'original'
    if ($format !== 'original' || $width || $height || $quality !== 90) {
        try {
            $image = Image::load($file['tmp_name']);

            var_dump($file['tmp_name']);

            // Zmień rozmiar, jeśli podano szerokość lub wysokość
            if ($width || $height) {
                $image->width($width)->height($height);
            }

            // Ustaw jakość
            $image->quality($quality);

            // Zapisz przetworzony obraz
            $extension = ($format !== 'original' ? $format : pathinfo($file['name'], PATHINFO_EXTENSION));
            $processedPath = $uploadDir . 'processed_' . pathinfo($file['name'], PATHINFO_FILENAME) . '.' . $extension;

            var_dump($processedPath);

            // Zapisz w wybranym formacie
            if ($format !== 'original') {
                $image->save($processedPath);
            } else {
                $image->save($processedPath);
            }

            echo "Obraz przetworzony i zapisany jako: <a href='$processedPath'>$processedPath</a>";
        } catch (Exception $e) {
            echo "Błąd podczas przetwarzania obrazu: " . $e->getMessage();
        }
    } else {
        echo "Obraz oryginalny zapisany jako: <a href='$originalPath'>$originalPath</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Test Spatie Image</title>
</head>
<body>
    <h1>Test uploadu i przetwarzania obrazu z Spatie Image</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="image">Wybierz plik obrazu:</label>
        <input type="file" name="image" id="image" required><br><br>

        <label for="width">Szerokość (opcjonalnie):</label>
        <input type="number" name="width" id="width"><br><br>

        <label for="height">Wysokość (opcjonalnie):</label>
        <input type="number" name="height" id="height"><br><br>

        <label for="quality">Jakość (1-100, domyślnie 90):</label>
        <input type="number" name="quality" id="quality" min="1" max="100" value="90"><br><br>

        <label for="format">Format:</label>
        <select name="format" id="format">
            <option value="original">Original (bez zmian)</option>
            <option value="jpg">JPG</option>
            <option value="png">PNG</option>
            <option value="webp">WEBP</option>
        </select><br><br>

        <button type="submit">Prześlij i przetwórz</button>
    </form>
</body>
</html>