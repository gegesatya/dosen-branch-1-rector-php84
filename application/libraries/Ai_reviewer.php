<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_reviewer {

    public function __construct() {
        // Initialization if needed
    }

    /**
     * Dapatkan daftar kandidat model aktif dengan mekanisme fallback berjenjang.
     * Jika model utama yang dipilih sudah deprecated / tidak berlaku lagi, 
     * sistem akan otomatis mencoba model cadangan berikutnya.
     */
    public function get_candidate_models($provider, $preferred_model = null) {
        $candidates = [];
        if (!empty($preferred_model)) {
            $candidates[] = trim($preferred_model);
        }

        switch ($provider) {
            case 'gemini':
                $defaults = [
                    'gemini-2.5-flash',
                    'gemini-2.0-flash',
                    'gemini-1.5-flash',
                    'gemini-flash-latest',
                    'gemini-3.5-flash',
                    'gemini-1.5-pro'
                ];
                break;

            case 'groq':
                $defaults = [
                    'openai/gpt-oss-120b',
                    'openai/gpt-oss-20b',
                    'qwen/qwen3.8-27b',
                    'qwen/qwen3.6-27b'
                ];
                break;

            case 'deepseek':
                $defaults = [
                    'deepseek-chat',
                    'deepseek-reasoner'
                ];
                break;

            case 'openai':
            default:
                $defaults = [
                    'gpt-4o',
                    'gpt-4o-mini',
                    'o3-mini',
                    'o1-mini',
                    'gpt-4-turbo'
                ];
                break;
        }

        foreach ($defaults as $m) {
            if (!in_array($m, $candidates)) {
                $candidates[] = $m;
            }
        }

        return $candidates;
    }

    /**
     * Mengambil daftar model aktif secara langsung (Live API Discovery) dari server provider AI.
     * Fitur ini menjamin sistem tidak akan pernah rusak meskipun provider mempensiunkan/mengubah nama model di masa depan.
     *
     * @param string $provider
     * @param string $api_key
     * @param bool $is_local
     * @return array List of model IDs
     */
    public function fetch_live_models($provider, $api_key, $is_local = false) {
        $live_models = [];
        $api_key = trim($api_key);
        if (empty($api_key)) return [];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            if ($is_local) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }

            if ($provider === 'gemini') {
                curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $api_key);
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code === 200) {
                    $json = json_decode($response, true);
                    if (!empty($json['models']) && is_array($json['models'])) {
                        $flash = [];
                        $pro   = [];
                        foreach ($json['models'] as $item) {
                            $name = str_replace('models/', '', $item['name'] ?? '');
                            $methods = $item['supportedGenerationMethods'] ?? [];
                            if (in_array('generateContent', $methods)) {
                                if (stripos($name, 'flash') !== false && stripos($name, 'embedding') === false) {
                                    $flash[] = $name;
                                } elseif (stripos($name, 'pro') !== false) {
                                    $pro[] = $name;
                                }
                            }
                        }
                        $live_models = array_merge($flash, $pro);
                    }
                }
            } elseif ($provider === 'groq') {
                curl_setopt($ch, CURLOPT_URL, 'https://api.groq.com/openai/v1/models');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $api_key
                ]);
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code === 200) {
                    $json = json_decode($response, true);
                    if (!empty($json['data']) && is_array($json['data'])) {
                        foreach ($json['data'] as $item) {
                            $id = $item['id'] ?? '';
                            if (!empty($id) && stripos($id, 'whisper') === false && stripos($id, 'guard') === false) {
                                $live_models[] = $id;
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Ai_reviewer fetch_live_models error: ' . $e->getMessage());
        }

        return $live_models;
    }

    /**
     * Susun URL, Headers, dan Payload cURL berdasarkan provider dan model aktif.
     */
    private function prepare_request_config($provider, $api_key, $model, $system_prompt, $user_document, $require_json = false) {
        $url = '';
        $headers = [];
        $payload = [];

        if ($provider === 'gemini') {
            $payload = [
                'contents' => [
                    ['parts' => [['text' => $system_prompt . "\n\n" . $user_document]]]
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE']
                ],
                'generationConfig' => [
                    'temperature' => 0.15,
                    'maxOutputTokens' => 16384
                ]
            ];

            if ($require_json) {
                $payload['generationConfig']['responseMimeType'] = 'application/json';
            }
            
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $api_key;
            $headers = [
                'Content-Type: application/json'
            ];
        } elseif ($provider === 'openai') {
            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user',   'content' => $user_document]
                ],
                'temperature' => 0.15,
                'max_tokens'  => 8192
            ];

            if ($require_json) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $url = 'https://api.openai.com/v1/chat/completions';
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ];
        } elseif ($provider === 'deepseek') {
            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user',   'content' => $user_document]
                ],
                'temperature' => 0.15,
                'max_tokens'  => 8192
            ];

            if ($require_json) {
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $url = 'https://api.deepseek.com/v1/chat/completions';
            $headers = [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ];
        } elseif ($provider === 'openai') {
            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system_prompt],
                    ['role' => 'user',   'content' => $user_document]
                ],
                'temperature' => 0.15,
                'max_tokens'  => 8192
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
                'temperature' => 0.15,
                'max_tokens'  => 8192
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

        return [
            'url'     => $url,
            'headers' => $headers,
            'payload' => $payload
        ];
    }

    /**
     * Generate an AI review using the selected provider.
     * Dilengkapi fitur Self-Healing Auto-Fallback: Jika model yang diminta sudah tidak berlaku (HTTP 404/400),
     * otomatis mencoba model alternatif yang masih aktif tanpa membuat request dosen gagal.
     *
     * @param string $provider 'gemini', 'groq', 'deepseek', or 'openai'
     * @param string $api_key API key for the provider
     * @param string $system_prompt The instructional prompt
     * @param string $user_document The actual document/context to be reviewed
     * @param bool $is_local Whether this is running on a local development environment
     * @param bool $require_json Whether the AI should be forced to output strict JSON
     * @param string|null $model Initial preferred model
     * @return array ['status' => boolean, 'msg' => string, 'review' => string (optional), 'used_model' => string]
     */
    public function generate_review($provider, $api_key, $system_prompt, $user_document, $is_local = false, $require_json = false, $model = null) {
        $api_key = trim($api_key ?? '');
        if (empty($api_key)) {
            return [
                'status' => false,
                'msg'    => 'API Key untuk AI Provider (' . strtoupper($provider) . ') belum dikonfigurasi pada akun Anda. Silakan masukkan API Key Anda di menu Pengaturan API Key.'
            ];
        }

        // Ambil urutan model kandidat (model preferensi -> model aktif cadangan)
        $candidate_models = $this->get_candidate_models($provider, $model);

        $last_http_code = 0;
        $last_response  = '';
        $last_curl_err  = '';
        $used_model     = '';

        foreach ($candidate_models as $current_model) {
            $used_model = $current_model;
            $req_config = $this->prepare_request_config($provider, $api_key, $current_model, $system_prompt, $user_document, $require_json);

            $url     = $req_config['url'];
            $headers = $req_config['headers'];
            $payload = $req_config['payload'];

            $max_retries = 1;
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
                curl_setopt($ch, CURLOPT_TIMEOUT, 75);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

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
                    sleep(2);
                    continue;
                }

                break;
            }

            $last_http_code = $http_code;
            $last_response  = $response;
            $last_curl_err  = $curl_error;

            // Jika sukses (200), langsung parsing dan return hasil
            if ($http_code === 200) {
                $result    = json_decode($response, true);
                $ai_review = null;
                $finish_reason = null;
                
                if ($provider === 'gemini') {
                    $ai_review = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    $finish_reason = $result['candidates'][0]['finishReason'] ?? null;
                } else {
                    // Groq, OpenAI, DeepSeek — all use OpenAI response format
                    $ai_review = $result['choices'][0]['message']['content'] ?? null;
                    $finish_reason = $result['choices'][0]['finish_reason'] ?? null;
                }

                if (!empty($ai_review)) {
                    // Bersihkan tag <think>...</think> jika ada (khusus DeepSeek Reasoner / R1)
                    $ai_review = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $ai_review);
                    $ai_review = trim($ai_review);

                    $is_truncated = !empty($finish_reason) && in_array(strtoupper($finish_reason), ['MAX_TOKENS', 'LENGTH']);

                    if ($finish_reason && strtolower($finish_reason) !== 'stop' && strtolower($finish_reason) !== 'end_turn') {
                        $ai_review .= "\n\n> [!WARNING]\n> **Proses Penyusunan Terpotong (Finish Reason: {$finish_reason})**\n> AI menghentikan pembuatan teks karena batas maksimum token respons telah tercapai. Klik tombol **Lanjutkan** di bawah untuk menyambung penyusunan dokumen secara otomatis.";
                    }

                    return [
                        'status'        => true,
                        'msg'           => 'Review AI berhasil digenerate.',
                        'review'        => $ai_review,
                        'used_model'    => $current_model,
                        'finish_reason' => $finish_reason,
                        'is_truncated'  => $is_truncated
                    ];
                }
            }

            // Cek apakah error karena model tidak ditemukan / deprecated / decommissioned (HTTP 404, 400 atau pesan model error)
            $resp_json = json_decode($response, true);
            $err_msg = $resp_json['error']['message'] ?? (is_string($resp_json['error'] ?? null) ? $resp_json['error'] : '');
            $is_model_error = ($http_code === 404) || 
                              stripos($err_msg, 'model') !== false && (stripos($err_msg, 'not found') !== false || stripos($err_msg, 'deprecated') !== false || stripos($err_msg, 'does not exist') !== false || stripos($err_msg, 'unsupported') !== false || stripos($err_msg, 'decommissioned') !== false || stripos($err_msg, 'no longer supported') !== false);

            if ($is_model_error) {
                // Model ini sudah tidak tersedia, lanjutkan ke model berikutnya dalam kandidat!
                log_message('debug', "Ai_reviewer: Model {$current_model} on {$provider} failed ({$http_code}). Trying next fallback model...");
                continue;
            }

            // Jika error auth (401/402/403) atau quota (429), ganti model tidak akan membantu, jadi break loop
            if (in_array($http_code, [401, 402, 403, 429])) {
                break;
            }
        }

        // Self-Healing Dynamic Discovery: Jika seluruh kandidat bawaan gagal karena model_error/404,
        // coba ambil daftar model aktif langsung dari API provider (Live Discovery)
        if (($last_http_code === 404 || stripos($last_response, 'model') !== false) && !in_array($last_http_code, [401, 402, 403, 429])) {
            $live_discovered = $this->fetch_live_models($provider, $api_key, $is_local);
            $new_models = array_diff($live_discovered, $candidate_models);
            if (!empty($new_models)) {
                log_message('debug', "Ai_reviewer: Attempting live discovered models for {$provider}: " . implode(', ', $new_models));
                foreach ($new_models as $discovered_model) {
                    $req_config = $this->prepare_request_config($provider, $api_key, $discovered_model, $system_prompt, $user_document, $require_json);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_URL, $req_config['url']);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $req_config['headers']);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_config['payload']));
                    curl_setopt($ch, CURLOPT_TIMEOUT, 75);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    if ($is_local) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response   = curl_exec($ch);
                    $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curl_error = curl_error($ch);
                    curl_close($ch);

                    if ($http_code === 200) {
                        $result    = json_decode($response, true);
                        $ai_review = null;
                        if ($provider === 'gemini') {
                            $ai_review = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        } else {
                            $ai_review = $result['choices'][0]['message']['content'] ?? null;
                        }

                        if (!empty($ai_review)) {
                            // Auto-heal: Simpan model baru yang valid ini ke DB
                            try {
                                if (function_exists('get_instance')) {
                                    $CI =& get_instance();
                                    if (isset($CI->db)) {
                                        $CI->db->where('ai_provider', $provider)
                                               ->where('api_key', $api_key)
                                               ->update('siakad_api_key', ['ai_model' => $discovered_model]);
                                    }
                                }
                            } catch (\Throwable $ex) {}

                            return [
                                'status'     => true,
                                'msg'        => 'Review AI berhasil digenerate (Auto-Discovered & Healed).',
                                'review'     => $ai_review,
                                'used_model' => $discovered_model
                            ];
                        }
                    }
                }
            }
        }

        // Jika seluruh model dalam rantai fallback gagal, tampilkan pesan error yang informatif
        $error_detail  = empty($last_curl_err) ? strip_tags($last_response) : $last_curl_err;
        $resp_json     = json_decode($last_response, true);
        $api_error_msg = $resp_json['error']['message'] ?? (is_string($resp_json['error'] ?? null) ? $resp_json['error'] : '');

        if ($last_http_code === 402 || stripos($api_error_msg, 'Insufficient Balance') !== false) {
            $friendly_msg = "⚠️ Saldo akun " . strtoupper($provider) . " Anda habis / bernilai $0 (HTTP 402: Insufficient Balance).\n\n"
                . "💡 Saran Solusi:\n"
                . "1. Lakukan isi saldo (top-up) di platform " . strtoupper($provider) . " (misal: https://platform.deepseek.com/top_up).\n"
                . "2. Atau beralih ke provider Google Gemini / Groq (Gratis) pada menu 'Setting API Key'.";
        } elseif ($last_http_code === 413 || stripos($api_error_msg, 'Request too large') !== false || stripos($api_error_msg, 'rate_limit_exceeded') !== false || stripos($api_error_msg, 'tokens per minute') !== false || stripos($api_error_msg, 'TPM') !== false) {
            $friendly_msg = "⚠️ Batas kuota token (Limit TPM) untuk provider " . strtoupper($provider) . " terlampaui.\n\n"
                . "💡 Saran Solusi:\n"
                . "1. Beralih ke provider lain seperti DeepSeek atau Google Gemini di menu 'Setting API Key'.";
        } elseif ($last_http_code === 429) {
            $friendly_msg = "⚠️ Batas request untuk " . strtoupper($provider) . " tercapai (HTTP 429: Rate Limit).\n\n"
                . "💡 Saran Solusi:\n"
                . "Silakan tunggu beberapa saat atau beralih ke provider lain pada menu 'Setting API Key'.";
        } elseif ($last_http_code === 503) {
            $friendly_msg = "⚠️ Server AI (" . strtoupper($provider) . ") sedang mengalami kepadatan trafik (HTTP 503).\n\n"
                . "💡 Saran Solusi:\n"
                . "Silakan coba lagi beberapa saat lagi atau beralih ke provider AI lain pada menu 'Setting API Key'.";
        } elseif ($last_http_code === 401 || $last_http_code === 403) {
            $friendly_msg = "⚠️ API Key untuk " . strtoupper($provider) . " tidak valid atau tidak memiliki izin akses (HTTP {$last_http_code}).\n\n"
                . "💡 Saran Solusi:\n"
                . "Silakan periksa dan perbarui API Key Anda pada menu 'Setting API Key'.";
        } elseif ($last_http_code === 404) {
            $raw_err = $api_error_msg ?: $error_detail;
            $friendly_msg = "⚠️ Model " . strtoupper($provider) . " tidak dapat diakses / tidak ditemukan (HTTP 404).\n\n"
                . "💡 Saran Solusi:\n"
                . "Silakan periksa konfigurasi model atau beralih ke provider Google Gemini / Groq / DeepSeek pada menu 'Setting API Key'.\n\nDetail: " . substr($raw_err, 0, 200);
        } else {
            $raw_err = $api_error_msg ?: $error_detail;
            $friendly_msg = "Gagal menghubungi API " . strtoupper($provider) . " (HTTP {$last_http_code}).\n\n"
                . "💡 Saran: Periksa API Key atau switch ke model/provider lain pada menu 'Setting API Key'.\n\nDetail: " . substr($raw_err, 0, 300);
        }

        return ['status' => false, 'msg' => $friendly_msg, 'suggest_switch' => true];
    }

}
