<?php
$secret = 'ethosylar1234@'; // must match GitHub webhook secret

// Verify the request
$headers = getallheaders();
$hubSignature = $headers['X-Hub-Signature-256'] ?? '';

$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret, false);

if (!hash_equals($hubSignature, $hash)) {
    http_response_code(403);
    exit('Invalid signature');
}

// Run git pull
exec('cd D:\Work\Server\xampp\htdocs\agreement && git pull 2>&1', $output, $return_var);

if ($return_var === 0) {
    echo "Pull successful:\n" . implode("\n", $output);
} else {
    http_response_code(500);
    echo "Pull failed:\n" . implode("\n", $output);
}
?>
