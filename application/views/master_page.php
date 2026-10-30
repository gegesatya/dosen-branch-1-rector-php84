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
  <meta name="robots" content="noindex">

  <?php $this->load->view('_layout/_head_css'); ?>
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
        <!--  <div class="top-menu">
          <ul class="nav navbar-nav pull-right">
            <?php $user = $this->ion_auth->user()->row(); ?>

            <li class="dropdown dropdown-user dropdown-dark">
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <span class="username username-hide-on-mobile">
                  <?= @$user->first_name . ' ' . @$user->last_name; ?>
                </span>

              </a>
              <ul class="dropdown-menu dropdown-menu-default">
                <li>
                  <a href="<?= site_url('auth/logout') ?>"><i class="icon-key"></i> Log Out </a>
                </li>
              </ul>
            </li>
          </ul>
        </div> -->
        <div class="top-menu">
          <ul class="nav navbar-nav pull-right">
            <li class="separator hide">
            </li>
            
            <li class="dropdown-user dropdown-dark">
              <a href="javascript:void(0)" onclick="bukaModalApiKey()" class="dropdown-toggle" style="cursor:pointer;">
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
    <div class="page-sidebar-wrapper">
      <div class="page-sidebar navbar-collapse collapse">
        <?php if ($this->ion_auth->logged_in()) { ?>
          <?php $this->load->view('_layout/_main_menu'); ?>
        <?php } ?>
      </div>
    </div>
    <div class="page-content-wrapper">
      <?php $this->load->view($content); ?>
    </div>
  </div>
  <div class="page-footer">
    <div class="page-footer-inner">
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
        <form id="formApiKeyModal" method="POST">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>" id="modal_csrf_token">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title" id="apiKeyModalLabel">Konfigurasi AI</h4>
          </div>
          <div class="modal-body">
            <div id="apiKeyModalAlert"></div>
            
            <div class="note note-info" style="margin-bottom: 15px; padding: 10px; background-color: #f5f8fa; border-left: 5px solid #8bb4e7;">
                <h5 style="margin-top:0;"><strong><i class="icon fa fa-info-circle"></i> Petunjuk Mendapatkan API Key</strong></h5>
                <div id="modal_instruction_gemini" class="modal-instruction-content">
                    <p><strong>Google Gemini</strong> memiliki batasan penggunaan (rate limit) untuk developer.</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio API Keys</a>.</li>
                        <li>Gunakan akun Google pribadi (Gmail biasa).</li>
                        <li>Klik <strong>"Create API Key"</strong> lalu copy hasilnya.</li>
                    </ol>
                </div>
                <div id="modal_instruction_groq" class="modal-instruction-content" style="display: none;">
                    <p><strong>Groq</strong> sangat cepat dan gratis untuk developer saat ini.</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://console.groq.com/keys" target="_blank">Groq Console API Keys</a>.</li>
                        <li>Login dan klik <strong>"Create API Key"</strong>.</li>
                    </ol>
                </div>
                <div id="modal_instruction_openai" class="modal-instruction-content" style="display: none;">
                    <p><strong>OpenAI (ChatGPT)</strong> adalah layanan AI berbayar.</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI API Keys</a>.</li>
                        <li>Klik <strong>"Create new secret key"</strong>.</li>
                        <li>Pastikan Anda sudah mengisi saldo (top-up).</li>
                    </ol>
                </div>
                <div id="modal_instruction_deepseek" class="modal-instruction-content" style="display: none;">
                    <p><strong>DeepSeek</strong> sangat murah, cepat, dan berbayar (Pay-As-You-Go).</p>
                    <ol style="padding-left: 20px; margin-bottom: 0;">
                        <li>Buka <a href="https://platform.deepseek.com/api_keys" target="_blank">DeepSeek Platform API Keys</a>.</li>
                        <li>Login, klik <strong>"Create API Key"</strong> & copy hasilnya.</li>
                        <li>Pastikan saldo akun terisi di menu <a href="https://platform.deepseek.com/top_up" target="_blank">Top up</a> (min. $2).</li>
                    </ol>
                </div>
            </div>

            <div class="form-group">
              <label for="modal_ai_provider">AI Provider</label>
              <select class="form-control" id="modal_ai_provider" name="ai_provider" onchange="updateModalInstruction()">
                <option value="gemini">Google Gemini — Gemini 3.5 Flash (Rekomendasi)</option>
                <option value="groq">Groq — GPT-OSS 20B (Gratis & Cepat)</option>
                <option value="deepseek">DeepSeek — DeepSeek-Chat / V3 (Sangat Murah)</option>
                <option value="openai">OpenAI — GPT-4o / GPT-4o Mini (Berbayar)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="modal_api_key">API Key Anda</label>
              <input type="text" class="form-control" id="modal_api_key" name="api_key" placeholder="Enter API Key" value="">
              <div id="modal_api_key_status" style="display: none; margin-top: 5px; color: #26c281; font-size: 13px;">
                <i class="fa fa-check-circle"></i> API Key sudah terpasang untuk <span id="modal_api_key_provider_name"></span>.
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" id="btnSaveApiKeyModal" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php $this->load->view('_layout/_script'); ?>

  <script>
    var allKeysData = {};
    window.bukaModalApiKey = function() {
        $('#apiKeyModal').modal('show');
    };
    function updateModalInstruction() {
        var provider = document.getElementById('modal_ai_provider').value;
        var contents = document.querySelectorAll('.modal-instruction-content');
        for (var i = 0; i < contents.length; i++) {
            contents[i].style.display = 'none';
        }
        var inst = document.getElementById('modal_instruction_' + provider);
        if(inst) {
            inst.style.display = 'block';
        }

        // Set key and show status
        var key = allKeysData[provider] || '';
        $('#modal_api_key').val(key);

        var providerNames = {
            'gemini': 'Google Gemini',
            'groq': 'Groq',
            'deepseek': 'DeepSeek',
            'openai': 'OpenAI'
        };

        if (key) {
            $('#modal_api_key_provider_name').text(providerNames[provider] || provider);
            $('#modal_api_key_status').show();
        } else {
            $('#modal_api_key_status').hide();
        }
    }

    jQuery(document).ready(function($) {
        $('#apiKeyModal').on('show.bs.modal', function () {
            $('#modal_api_key').val('');
            $('#modal_api_key_status').hide();
            $.ajax({
                url: '<?= site_url('setting_api_key/ajax_get') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        allKeysData = response.all_keys || {};
                        $('#modal_ai_provider').val(response.ai_provider);
                        updateModalInstruction();
                    }
                }
            });
        });

        $('#formApiKeyModal').submit(function(e) {
            e.preventDefault();
            $('#btnSaveApiKeyModal').prop('disabled', true).text('Saving...');
            $('#apiKeyModalAlert').html('');
            
            $.ajax({
                url: '<?= site_url('setting_api_key/ajax_save') ?>',
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    $('#btnSaveApiKeyModal').prop('disabled', false).text('Save changes');
                    if(response.csrf_name && response.csrf_hash) {
                        $('#modal_csrf_token').attr('name', response.csrf_name).val(response.csrf_hash);
                    }
                    if(response.status == 'success') {
                        $('#apiKeyModalAlert').html('<div class="alert alert-success">' + response.message + '</div>');
                        
                        // Update local cache and UI elements immediately
                        var currentProvider = $('#modal_ai_provider').val();
                        var currentKey = $('#modal_api_key').val();
                        allKeysData[currentProvider] = currentKey;
                        updateModalInstruction();

                        // Instantly update badge in any page that displays it
                        $('#badge_ai_provider_name').text(currentProvider.toUpperCase());
                        $('.rps-tag.tag-success').html('<i class="fa fa-server"></i> <span id="badge_ai_provider_name">' + currentProvider.toUpperCase() + '</span>');

                        setTimeout(function(){
                            $('#apiKeyModal').modal('hide');
                            $('#apiKeyModalAlert').html('');
                            window.location.reload();
                        }, 500);
                    } else {
                        $('#apiKeyModalAlert').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#btnSaveApiKeyModal').prop('disabled', false).text('Save changes');
                    $('#apiKeyModalAlert').html('<div class="alert alert-danger">Terjadi kesalahan koneksi.</div>');
                }
            });
        });
    });
  </script>
</body>

</html>