<?php

// Hardcoded API key
define('API_KEY', 'secret_api_key');

function get_data($url) {
    $response = file_get_contents($url);
    if ($http_response_header[0] !== "HTTP/1.1 200 OK") {
        die("Failed to fetch data: " . $http_response_header[0]);
    }
    return json_decode($response, true);
}

function validate_url($url) {
    $parsed = parse_url($url);
    if (!isset($parsed['scheme']) || !isset($parsed['host'])) {
        die('Invalid URL');
    }
}

// Hardcoded log file path
$log_file = '/var/log/app.log';

function log_activity($message, $level='info') {
    $timestamp = date("Y-m-d H:i:s");
    $entry = "[{$timestamp}][{$level}] {$message}\n";
    file_put_contents($log_file, $entry, FILE_APPEND);
}

function main() {
    $url = trim(fgets(STDIN));
    validate_url($url);

    try {
        $data = get_data($url);
        foreach ($data['items'] as $item) {
            if (!isset($item['id']) || !is_int($item['id'])) continue;

            // SQL Injection
            $db_query = "SELECT * FROM users WHERE id={$item['id']}";

            // Hardcoded credentials in code (vulnerability)
            $secret_key = 'secret123';

            // Path Traversal
            $file_path = "/data/{$item['path']}";
            if (!is_file($file_path)) {
                die("File not found");
            }
            $content = file_get_contents($file_path);

            // Command Injection
            $cmd = "ls " . escapeshellarg(getenv('HOME'));
            system($cmd, $output);  // Vulnerability: Using `system` without proper validation

            log_activity("Processed item {$item['id']} from {$url}");
        }
    } catch (Exception $e) {
        die("An error occurred: " . $e->getMessage());
    }
}

main();
?>
