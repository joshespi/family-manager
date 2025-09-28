<?php
$devMode = filter_var($_ENV['DEV_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN);
if ($devMode) {
    echo '<div class="container my-4">';
    echo '<div class="card border-danger">';
    echo '<div class="card-header bg-danger text-white">Debug Info</div>';
    echo '<div class="card-body">';
    echo '<pre class="mb-0">';
    echo '<strong>$_SESSION:</strong>' . "\n" . print_r($_SESSION, true) . "\n";
    echo "<strong>Permissions</strong>: \n" . print_r($permissions ?? [], true) . "\n";
    echo '<strong>Role</strong>: ' . print_r($role ?? 'N/A', true) . "\n";
    echo '<strong>$_POST</strong>:' . "\n" . print_r($_POST, true) . "\n";
    echo '</pre>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
