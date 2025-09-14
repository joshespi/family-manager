<?php
function render($view, $params = [])
{
    $params['app_Version'] = $_ENV['APP_VERSION'];
    extract($params);
    ob_start();
    require __DIR__ . "/$view.php";
    $content = ob_get_clean();
    require __DIR__ . "/layout.php";
}
