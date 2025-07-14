<?php
$uploads_dir = 'uploads';
$posts_file = 'posts.txt';

// Create uploads dir if not exists
if (!file_exists($uploads_dir)) {
    mkdir($uploads_dir, 0777, true);
}

// Handle new uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dog_image'])) {
    $caption = htmlspecialchars($_POST['caption'] ?? '');
    $image = $_FILES['dog_image'];

    if ($image['error'] == UPLOAD_ERR_OK && $image['type'] && strpos($image['type'], 'image/') === 0) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $safe_name = uniqid("dog_", true) . "." . strtolower($ext);
        $dest = $uploads_dir . "/" . $safe_name;
        if (move_uploaded_file($image['tmp_name'], $dest)) {
            $line = $dest . "|" . $caption . PHP_EOL;
            file_put_contents($posts_file, $line, FILE_APPEND);
        }
    }
}

// Load all posts
$posts = [];
if (file_exists($posts_file)) {
    $lines = file($posts_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        [$img, $caption] = explode('|', $line, 2);
        $posts[] = ['img' => $img, 'caption' => $caption];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Street Animal Rescue - Upload a Dog for Rescue</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    .adopt-info {
        display: none;
        margin-top: 12px;
        background: #eaf6ff;
        border-radius: 7px;
        padding: 12px;
        font-size: 15px;
        color: #1976d2;
    }
    .adopt-btn {
        background: #4caf50;
        color: white;
        padding: 7px 20px;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 8px;
    }
    .adopt-btn:hover {
        background: #388e3c;
    }
    </style>
</head>
<body>
    <h1>Rescue Street Animals 🐾</h1>
    <form method="post" enctype="multipart/form-data" style="margin: 0 auto; max-width: 420px;">
        <input type="file" name="dog_image" accept="image/*" required>
        <br><br>
        <input type="text" name="caption" placeholder="Caption or location..." maxlength="120" style="width:90%;padding:7px;">
        <br><br>
        <button type="submit">Upload Dog</button>
    </form>
    <h2>Dogs Needing Adoption</h2>
    <div id="animal-container">
        <?php if (count($posts) === 0): ?>
            <p>No dogs posted yet. Be the first to upload!</p>
        <?php else: ?>
            <?php
            $adopt_info = "To adopt this dog, please visit our shelter at 123 Rescue Lane, Cityville or contact us at 0917-123-4567. Requirements: Valid ID, proof of address, and love for animals! For faster processing, send us a message on our Facebook page: <a href='https://facebook.com/your-rescue-page' target='_blank'>Rescuers PH</a>.";
            foreach (array_reverse($posts) as $i => $post): ?>
                <div class="animal-card">
                    <img src="<?= htmlspecialchars($post['img']) ?>" alt="Dog photo">
                    <p><?= htmlspecialchars($post['caption']) ?></p>
                    <button class="adopt-btn" onclick="showAdoptInfo(<?= $i ?>)">Adopt me</button>
                    <div class="adopt-info" id="adopt-info-<?= $i ?>">
                        <?= $adopt_info ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <script>
    function showAdoptInfo(idx) {
        // Hide all adopt info boxes first
        document.querySelectorAll('.adopt-info').forEach(function(box) {
            box.style.display = 'none';
        });
        // Show only the selected one
        document.getElementById('adopt-info-' + idx).style.display = 'block';
    }
    </script>
</body>
</html>
