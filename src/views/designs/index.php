<?php
/**
 * Designs View - Placeholder
 */

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designs - Aislum Studio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="/css/custom.css">
    <?php require __DIR__ . '/../components/nav_styles.php'; ?>
    <style>
        main { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { margin: 0; color: #333; }
        .placeholder-content { background: white; border: 1px solid #e0e0e0; border-radius: 0.5rem; padding: 3rem; text-align: center; color: #999; }
        .placeholder-content h2 { color: #666; }
    </style>
</head>
<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <main>
        <div class="page-header">
            <h1>My Designs</h1>
        </div>
        <div class="placeholder-content">
            <h2>🎨 Design Studio</h2>
            <p>Design tools are coming soon!</p>
            <p>You'll be able to create and edit business cards, flyers, and other designs here.</p>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
