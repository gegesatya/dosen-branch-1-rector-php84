<div class="page-content" style="padding: 0 0 0 20px;">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-key font-green-sharp"></i>
                        <span class="caption-subject font-green-sharp bold uppercase">Pengaturan Google AI API Key</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <?php if($this->session->flashdata('success')): ?>
                        <div class="alert alert-success">
                            <i class="fa fa-check"></i> <?= $this->session->flashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger">
                            <i class="fa fa-warning"></i> <?= $this->session->flashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="note note-info" id="instructions_box">
                        <h4 class="block"><i class="icon fa fa-info-circle"></i> Petunjuk Mendapatkan API Key</h4>
                        <div id="instruction_gemini" class="instruction-content">
                            <p><strong>Google Gemini</strong> memiliki batasan penggunaan (rate limit) untuk developer dan memiliki konteks jendela yang sangat besar.</p>
                            <ol style="margin-top: 10px;">
                                <li>Buka halaman <strong><a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio API Keys</a></strong>.</li>
                                <li><strong>Sangat Disarankan:</strong> Gunakan akun Google pribadi (Gmail biasa), BUKAN akun GSuite kampus.</li>
                                <li>Klik tombol <strong>"Create API Key"</strong>.</li>
                                <li>Copy kombinasi huruf dan angka panjang yang muncul (itulah API Key Anda).</li>
                            </ol>
                        </div>
                        <div id="instruction_groq" class="instruction-content" style="display: none;">
                            <p><strong>Groq</strong> sangat cepat dan 100% gratis untuk developer saat ini.</p>
                            <ol style="margin-top: 10px;">
                                <li>Buka halaman <strong><a href="https://console.groq.com/keys" target="_blank">Groq Console API Keys</a></strong>.</li>
                                <li>Login dan klik tombol <strong>"Create API Key"</strong>.</li>
                                <li>Copy API Key yang muncul.</li>
                            </ol>
                        </div>
                        <div id="instruction_openai" class="instruction-content" style="display: none;">
                            <p><strong>OpenAI (ChatGPT)</strong> adalah layanan AI berbayar dengan model paling populer di dunia. Model <strong>gpt-4o-mini</strong> yang kami gunakan sangat terjangkau namun tetap sangat powerful.</p>
                            <ol style="margin-top: 10px;">
                                <li>Buka halaman <strong><a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform API Keys</a></strong>.</li>
                                <li>Daftar / login menggunakan email Anda.</li>
                                <li>Klik <strong>"Create new secret key"</strong>, beri nama (misal: <em>wbi-lis</em>), lalu klik Create.</li>
                                <li>Copy API Key yang muncul &mdash; key hanya ditampilkan <strong>sekali</strong>.</li>
                                <li><strong>Top-up saldo terlebih dahulu</strong> &mdash; OpenAI adalah layanan berbayar. Isi saldo minimal $5 di menu Billing untuk mulai menggunakan.</li>
                            </ol>
                        </div>
                    </div>

                    <form method="post" action="<?= base_url('setting_api_key') ?>" class="form-horizontal">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                        
                        <?php 
                            $provider = !empty($current_key) && isset($current_key->ai_provider) ? $current_key->ai_provider : 'gemini';
                            $key_val = !empty($current_key) && isset($current_key->api_key) ? $current_key->api_key : '';
                            $model_val = !empty($current_key) && isset($current_key->ai_model) ? $current_key->ai_model : '';
                        ?>

                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">AI Provider</label>
                                <div class="col-md-5">
                                    <select class="form-control" name="ai_provider" id="ai_provider">
                                        <option value="gemini"   <?= $provider == 'gemini'   ? 'selected' : '' ?>>Google Gemini (Berbatas)</option>
                                        <option value="groq"     <?= $provider == 'groq'     ? 'selected' : '' ?>>Groq &mdash; Llama 3.3 70B (Gratis)</option>
                                        <option value="openai" <?= $provider == 'openai' ? 'selected' : '' ?>>OpenAI &mdash; GPT-4o Mini (Berbayar)</option>
                                    </select>
                                    <span class="help-block">Model otomatis: Gemini 2.5 Flash / Llama 3.3 70B / GPT-4o Mini.</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">API Key Anda</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" id="api_key" name="api_key" placeholder="Paste API Key di sini..." value="<?= htmlspecialchars($key_val) ?>" required>
                                    <?php if(!empty($key_val)): ?>
                                        <span class="help-block font-green"><i class="fa fa-check-circle"></i> API Key sudah terpasang.</span>
                                    <?php else: ?>
                                        <span class="help-block font-red"><i class="fa fa-times-circle"></i> Belum ada API Key terpasang.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions text-right" style="padding-top: 20px;">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-5 text-left">
                                    <button type="submit" name="submit" value="1" class="btn green"><i class="fa fa-save"></i> Simpan Konfigurasi</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('ai_provider');
    const apiKeyInput = document.getElementById('api_key');
    const helpBlock = apiKeyInput.nextElementSibling;
    
    const storedKeys = <?= json_encode($all_keys ?? []) ?>;

    const providerLabels = { gemini: 'Google Gemini', groq: 'Groq', openai: 'OpenAI' };

    function updateView() {
        const provider = providerSelect.value;
        
        // Update instructions
        document.querySelectorAll('.instruction-content').forEach(el => el.style.display = 'none');
        const instBox = document.getElementById('instruction_' + provider);
        if (instBox) instBox.style.display = 'block';
        
        // Update input value based on stored keys
        const savedKey = storedKeys[provider] || '';
        apiKeyInput.value = savedKey;
        
        // Update helper text
        const label = providerLabels[provider] || provider;
        if (savedKey) {
            helpBlock.className = 'help-block font-green';
            helpBlock.innerHTML = '<i class="fa fa-check-circle"></i> API Key sudah terpasang untuk ' + label + '.';
        } else {
            helpBlock.className = 'help-block font-red';
            helpBlock.innerHTML = '<i class="fa fa-times-circle"></i> Belum ada API Key terpasang untuk ' + label + '.';
        }
    }

    providerSelect.addEventListener('change', updateView);
    updateView();
});
</script>
