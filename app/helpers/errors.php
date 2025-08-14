<?php
/**
 * Show 404 page
 */
function show404() {
    http_response_code(404);
    require_once __DIR__ . '/../../views/404.php';
    exit;
}
