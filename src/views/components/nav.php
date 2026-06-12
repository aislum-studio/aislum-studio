<?php
/**
 * Navigation Component
 *
 * Include this in every authenticated view:
 *   require __DIR__ . '/../components/nav.php';
 *
 * Expects $currentUser to already be set in the calling view.
 * Automatically highlights the active nav link based on $_GET['page'].
 */

$_navPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

function navLink($label, $page, $currentPage) {
    $active = ($currentPage === $page)
        ? ' style="color:#667eea;font-weight:bold;"'
        : '';
    return '<li><a href="?page=' . $page . '"' . $active . '>' . $label . '</a></li>';
}
?>
<nav>
    <div class="nav-container">
        <a href="?page=dashboard" class="nav-brand">Aislum Studio</a>

        <ul class="nav-links">
            <?php
            echo navLink('Dashboard', 'dashboard', $_navPage);
            echo navLink('Documents', 'documents', $_navPage);
            echo navLink('Designs',   'designs',   $_navPage);
            ?>
        </ul>

        <div class="nav-user">
            <span><?php echo sanitize($currentUser['username']); ?></span>
            <a href="?page=profile"<?php echo $_navPage === 'profile' ? ' style="color:#667eea;font-weight:bold;"' : ''; ?>>Profile</a>
            <a href="?page=auth&action=logout">Logout</a>
        </div>
    </div>
</nav>
