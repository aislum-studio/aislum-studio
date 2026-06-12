<?php
/**
 * Nav Styles Component
 *
 * Include inside <head> of every authenticated view:
 *   require __DIR__ . '/../components/nav_styles.php';
 */
?>
<style>
    nav {
        background-color: #fff;
        border-bottom: 1px solid #e0e0e0;
        padding: 1rem 0;
    }
    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .nav-brand {
        font-size: 1.5rem;
        font-weight: bold;
        color: #667eea;
        text-decoration: none;
    }
    .nav-brand:hover { color: #764ba2; }
    .nav-links {
        display: flex;
        gap: 2rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .nav-links a { text-decoration: none; color: #333; font-weight: 500; }
    .nav-links a:hover { color: #667eea; }
    .nav-user { display: flex; gap: 1rem; align-items: center; }
    .nav-user a { text-decoration: none; color: #333; font-weight: 500; }
    .nav-user a:hover { color: #667eea; }
    @media (max-width: 768px) {
        .nav-container { flex-direction: column; gap: 0.75rem; }
        .nav-links { gap: 1rem; flex-wrap: wrap; justify-content: center; }
        .nav-user { flex-direction: column; gap: 0.5rem; }
    }
</style>
