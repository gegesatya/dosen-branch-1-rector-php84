<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->


<head>
  <meta charset="utf-8" />
  <title>Akademik Dosen WBI</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <meta content="" name="description" />
  <meta content="" name="author" />
  <meta name="robots" content="noindex">
  <?php $this->load->view('_layout/_head_css', '', FALSE); ?>
</head>












<body class="page-header-fixed page-sidebar-closed-hide-logo ">
  <div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner">
      <div class="page-logo">
        <a href="#">
          <img src="<?= base_url('assets'); ?>/image_web/logo-white-3.png" alt="logo" width="150px" class="logo-default" />
        </a>
        <div class="menu-toggler sidebar-toggler">
        </div>
      </div>
      <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
      </a>
      <div class="page-top">
        <div class="top-menu">
          <ul class="nav navbar-nav pull-right">
            <li class="separator hide">
            </li>
            
            <li class="dropdown dropdown-user dropdown-dark">
              <a data-toggle="modal" href="#apiKeyModal" class="dropdown-toggle" style="cursor:pointer;">
                <i class="icon-settings"></i>
                <span class="username username-hide-on-mobile">API Key</span>
              </a>
            </li>
            

            
            <li class="separator hide">
            </li>
            <li class="dropdown dropdown-user dropdown-dark">
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <span class="username username-hide-on-mobile">
                  <?= @$user->first_name . ' ' . @$user->last_name; ?> </span>
                
                
              </a>
              <ul class="dropdown-menu dropdown-menu-default">
                <li>
                  <a href="<?= site_url('auth/logout') ?>"><i class="icon-key"></i> Log Out </a>
                </li>
              </ul>
            </li>
            
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="clearfix">
  </div>
  <div class="page-container">
    <div class="col-md-8 col-md-offset-2">
      <?php $this->load->view($content, '', FALSE); ?>
    </div>

  </div>
  <div class="page-footer">
    <div class="page-footer-inner col-md-8 col-md-offset-2">
      <?= date('Y'); ?> Bagian Teknologi Informasi Wilmar Bisnis Indonesia
    </div>
    <div class="scroll-to-top">
      <i class="icon-arrow-up"></i>
    </div>
  </div>

  <!-- API Key Modal -->
  <div class="modal fade" id="apiKeyModal" tabindex="-1" role="dialog" aria-labelledby="apiKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="formApiKeyModal2" method="POST">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>" id="modal_csrf_token_2">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title" id="apiKeyModalLabel">Konfigurasi AI</h4>
          </div>
          <div class="modal-body">
            <div id="apiKeyModalAlert2"></div>
            
            <div class="note note-info" style="margin-bottom: 15px; padding: 10px; background-color: #f5f8fa; border-left: 5px solid #8bb4e7;">
                <h5 style="margin-top:0;"><strong><i class="icon fa fa-info-circle"></i> Petunjuk Mendapatkan API Key</strong></h5>
                <div id="modal_instruction_gemini_2" class="modal-instruction-content-2">
                    <p><strong>Google Gemini</strong> memiliki batasan penggunaan (rate limit) untuk developer.</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio API Keys</a>.</li>
                        <li>Gunakan akun Google pribadi (Gmail biasa).</li>
                        <li>Klik <strong>"Create API Key"</strong> lalu copy hasilnya.</li>
                    </ol>
                </div>
                <div id="modal_instruction_groq_2" class="modal-instruction-content-2" style="display: none;">
                    <p><strong>Groq</strong> sangat cepat dan gratis untuk developer saat ini.</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://console.groq.com/keys" target="_blank">Groq Console API Keys</a>.</li>
                        <li>Login dan klik <strong>"Create API Key"</strong>.</li>
                    </ol>
                </div>
                <div id="modal_instruction_openai_2" class="modal-instruction-content-2" style="display: none;">
                    <p><strong>OpenAI (ChatGPT)</strong> adalah layanan AI berbayar.</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI API Keys</a>.</li>
                        <li>Klik <strong>"Create new secret key"</strong>.</li>
                        <li>Pastikan Anda sudah mengisi saldo (top-up).</li>
                    </ol>
                </div>
                <div id="modal_instruction_deepseek_2" class="modal-instruction-content-2" style="display: none;">
                    <p><strong>DeepSeek</strong> sangat murah, cepat, dan berbayar (Pay-As-You-Go).</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://platform.deepseek.com/api_keys" target="_blank">DeepSeek Platform API Keys</a>.</li>
                        <li>Login, klik <strong>"Create API Key"</strong> & copy hasilnya.</li>
                        <li>Pastikan saldo akun terisi di menu <a href="https://platform.deepseek.com/top_up" target="_blank">Top up</a> (min. $2).</li>
                    </ol>
                </div>
            </div>

             <div class="form-group">
              <label for="modal_ai_provider_2">AI Provider</label>
              <select class="form-control" id="modal_ai_provider_2" name="ai_provider" onchange="updateModalInstruction2()">
                <option value="gemini">Google Gemini — Gemini 3.5 Flash (Rekomendasi)</option>
                <option value="groq">Groq — GPT-OSS 20B (Gratis & Cepat)</option>
                <option value="deepseek">DeepSeek — DeepSeek-Chat / V3 (Sangat Murah)</option>
                <option value="openai">OpenAI — GPT-4o / GPT-4o Mini (Berbayar)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="modal_api_key_2">API Key Anda</label>
              <input type="text" class="form-control" id="modal_api_key_2" name="api_key" placeholder="Enter API Key" value="">
              <div id="modal_api_key_status_2" style="display: none; margin-top: 5px; color: #26c281; font-size: 13px;">
                <i class="fa fa-check-circle"></i> API Key sudah terpasang untuk <span id="modal_api_key_provider_name_2"></span>.
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="btnSaveApiKeyModal2" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php $this->load->view('_layout/_script', '', FALSE); ?>

  <script>
    var allKeysData2 = {};
    function updateModalInstruction2() {
        var provider = document.getElementById('modal_ai_provider_2').value;
        var contents = document.querySelectorAll('.modal-instruction-content-2');
        for (var i = 0; i < contents.length; i++) {
            contents[i].style.display = 'none';
        }
        var inst = document.getElementById('modal_instruction_' + provider + '_2');
        if(inst) {
            inst.style.display = 'block';
        }

        // Set key and show status
        var key = allKeysData2[provider] || '';
        $('#modal_api_key_2').val(key);

        var providerNames = {
            'gemini': 'Google Gemini',
            'groq': 'Groq',
            'deepseek': 'DeepSeek',
            'openai': 'OpenAI'
        };

        if (key) {
            $('#modal_api_key_provider_name_2').text(providerNames[provider] || provider);
            $('#modal_api_key_status_2').show();
        } else {
            $('#modal_api_key_status_2').hide();
        }
    }

    jQuery(document).ready(function($) {
        $('#apiKeyModal').on('show.bs.modal', function () {
            $('#modal_api_key_2').val('');
            $('#modal_api_key_status_2').hide();
            $.ajax({
                url: '<?= site_url('setting_api_key/ajax_get') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        allKeysData2 = response.all_keys || {};
                        $('#modal_ai_provider_2').val(response.ai_provider);
                        updateModalInstruction2();
                    }
                }
            });
        });

        $('#formApiKeyModal2').submit(function(e) {
            e.preventDefault();
            $('#btnSaveApiKeyModal2').prop('disabled', true).text('Saving...');
            $('#apiKeyModalAlert2').html('');
            
            $.ajax({
                url: '<?= site_url('setting_api_key/ajax_save') ?>',
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    $('#btnSaveApiKeyModal2').prop('disabled', false).text('Save changes');
                    if(response.csrf_name && response.csrf_hash) {
                        $('#modal_csrf_token_2').attr('name', response.csrf_name).val(response.csrf_hash);
                    }
                    if(response.status == 'success') {
                        $('#apiKeyModalAlert2').html('<div class="alert alert-success">' + response.message + '</div>');
                        
                        // Update local cache and UI elements immediately
                        var currentProvider = $('#modal_ai_provider_2').val();
                        var currentKey = $('#modal_api_key_2').val();
                        allKeysData2[currentProvider] = currentKey;
                        updateModalInstruction2();

                        // Instantly update badge in any page that displays it
                        $('#badge_ai_provider_name').text(currentProvider.toUpperCase());
                        $('.rps-tag.tag-success').html('<i class="fa fa-server"></i> <span id="badge_ai_provider_name">' + currentProvider.toUpperCase() + '</span>');

                        setTimeout(function(){
                            $('#apiKeyModal').modal('hide');
                            $('#apiKeyModalAlert2').html('');
                            window.location.reload();
                        }, 500);
                    } else {
                        $('#apiKeyModalAlert2').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#btnSaveApiKeyModal2').prop('disabled', false).text('Save changes');
                    $('#apiKeyModalAlert2').html('<div class="alert alert-danger">Terjadi kesalahan koneksi.</div>');
                }
            });
        });
    });
  </script>
</body>

</html>