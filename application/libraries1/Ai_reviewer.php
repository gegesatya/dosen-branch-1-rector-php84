<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_reviewer {

    public function __construct() {
        // Initialization if needed
    }

    /**
     * Generate an AI review using the selected provider.
     *
     * @param string $provider 'gemini', 'groq', or 'openai'
     * @param string $api_key API key for the provider
     * @param string $system_prompt The instructional prompt
     * @param string $user_document The actual document/context to be reviewed
     * @param bool $is_local Whether this is running on a local development environment
     * @param bool $require_json Whether the AI should be forced to output strict JSON
     * @return array ['status' => boolean, 'msg' => string, 'review' => string (optional)]
     */
    public function generate_review($provider, $api_key, $system_prompt, $user_document, $is_local = false, $require_json = false) {
        // Default API Key Groq jika dosen belum mengkonfigurasi
        if ($provider === 'groq' && empty(trim($api_key))) {
            $api_key = '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';
        }

        // Define Model defaults based on provider
        if ($provider === 'groq') {
            $model = 'openai/gpt-oss-120b';
        } elseif ($provider === 'openai') {
            $model = 'gpt-4o-mini'; // Affordable & capable OpenAI model
        } else {
            $model = 'gemini-3.6-flash';
        }

        if ($provider === 'gemini') {
            $payload = [
                'contents' => [
                    ['parts' => [['text' => $system_prompt . "\n\n" . $user_document]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 8192
                ]
            ];

            if ($require_json) {
                $payload['generationConfig']['responseMimeType'] = 'application/json';
            }
            
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $api_key;
            $headers = [
                'Content-Type: application/json',
                'Api-Revision: 2026-05-20'
            ];
        } elseif ($provider === 'openai') {
            // OpenAI — official API
            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user',   'content' => $user_document]
                ],
                'temperature' => 0.4,
                'max_tokens'  => 4096
            ];

            if ($require_json) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $url = 'https://api.openai.com/v1/chat/completions';
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ];
        } else {
            // Groq — OpenAI-compatible format
            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user',   'content' => $user_document]
                ],
                'temperature' => 0.4,
                'max_tokens'  => 4096
            ];

            if ($require_json) {
                $payload['response_format'] = ['type' => 'json_object'];
            }
            
            $url = 'https://api.groq.com/openai/v1/chat/completions';
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ];
        }

        // Retry logic: up to 3 attempts for 503/429 errors (server overload / rate limit)
        $max_retries = 3;
        $http_code   = 0;
        $response    = '';
        $curl_error  = '';

        for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 90); // 90 detik untuk dokumen besar
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

            if ($is_local) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }

            $response   = curl_exec($ch);
            $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            // Success
            if ($http_code === 200) {
                break;
            }

            // Retry hanya jika 503 (overload) atau 429 (rate limit)
            if (in_array($http_code, [503, 429]) && $attempt < $max_retries) {
                // Exponential backoff: 2s, 4s, 8s
                $wait_seconds = pow(2, $attempt);
                sleep($wait_seconds);
                continue;
            }

            // Untuk error lain (401, 400, dsb) langsung berhenti
            break;
        }

        if ($http_code !== 200) {
            $error_detail = empty($curl_error) ? strip_tags($response) : $curl_error;

            // Pesan error yang lebih ramah untuk kasus overload
            if ($http_code === 503) {
                $friendly_msg = 'Server AI sedang padat (HTTP 503). Sistem telah mencoba ' . $max_retries . 'x secara otomatis. Silakan tunggu beberapa menit lalu coba lagi.';
            } elseif ($http_code === 429) {
                $friendly_msg = 'Batas penggunaan API tercapai (HTTP 429). Silakan tunggu sebentar lalu coba lagi.';
            } else {
                $friendly_msg = 'Gagal menghubungi API ' . strtoupper($provider) . ' (HTTP ' . $http_code . '). Pastikan API Key valid. Error: ' . $error_detail;
            }

            return ['status' => false, 'msg' => $friendly_msg];
        }

        $result    = json_decode($response, true);
        $ai_review = null;
        
        if ($provider === 'gemini') {
            $ai_review = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } else {
            // Groq, OpenAI — all use OpenAI response format
            $ai_review = $result['choices'][0]['message']['content'] ?? null;
        }

        if (!empty($ai_review)) {
            return [
                'status' => true,
                'msg'    => 'Review AI berhasil digenerate.',
                'review' => $ai_review
            ];
        }

        return [
            'status' => false,
            'msg'    => 'Berhasil menghubungi AI, tetapi response kosong atau format tidak sesuai.'
        ];
    }
}
