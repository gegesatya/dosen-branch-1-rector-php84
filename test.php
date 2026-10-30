<?php
define('BASEPATH', true);
require 'application/config/database.php';
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
$res = $conn->query("SELECT api_key FROM siakad_api_key WHERE ai_provider = 'gemini' LIMIT 1");
if (!$res) { echo "DB Error: " . $conn->error; exit; }
$row = $res->fetch_assoc();
$api_key = $row['api_key'] ?? '';
if (empty($api_key)) { echo "No API key found\n"; exit; }

$url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $api_key;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$output = curl_exec($ch);
curl_close($ch);
$data = json_decode($output, true);
if (isset($data['models'])) {
    foreach ($data['models'] as $m) {
        if (in_array('generateContent', $m['supportedGenerationMethods'])) {
            echo $m['name'] . "\n";
        }
    }
} else {
    echo "Error or no models: " . substr($output, 0, 500);
}
