<?php
define('DEPLOY_TOKEN', 'ec8c4d6f4961b6b26fbb3556c90ecea6f36f39cf39c317f2b9ccc676b5b42844');

$token = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';
if (!hash_equals(DEPLOY_TOKEN, $token)) {
    http_response_code(403);
    exit('Forbidden');
}

$output = shell_exec('cd /data/coderush-sites && git pull origin main 2>&1 && npm run build:css 2>&1 && docker compose restart 2>&1');

file_put_contents('/var/log/deploy-coderush.log', date('Y-m-d H:i:s') . "\n" . $output . "\n---\n", FILE_APPEND);

http_response_code(200);
echo 'OK';
