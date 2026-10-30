<!-- Modal Summary RPS OBE & Evaluasi 16 Pertemuan -->
<div class="modal fade" id="modal_summary_rps" tabindex="-1" role="dialog" aria-labelledby="modalSummaryRpsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 88%; max-width: 1050px;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #fff; padding: 15px 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff; opacity:0.8; font-size:24px;">&times;</button>
                <h4 class="modal-title" id="modalSummaryRpsLabel" style="font-weight: 700; font-size: 16px; margin: 0; display:flex; align-items:center;">
                    <i class="fa fa-pie-chart" style="margin-right: 8px;"></i> Ringkasan &amp; Evaluasi RPS
                </h4>
            </div>

            <div class="modal-body" style="background: #f8fafc; padding: 18px 22px; max-height: calc(85vh - 120px); overflow-y: auto;">
                <div id="summary_loading" class="text-center" style="padding: 40px 0;">
                    <i class="fa fa-spinner fa-spin fa-3x fa-fw text-primary"></i>
                    <p style="margin-top: 12px; color: #64748b; font-weight: 500;">Menganalisis matriks RPS, Bobot 16 Pertemuan, dan Penyelarasan CPL...</p>
                </div>

                <div id="summary_error" class="alert alert-danger" style="display:none; border-radius: 6px;"></div>

                <div id="summary_content" style="display:none;">
                    
                    <!-- Header Info Box -->
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div class="row">
                            <div class="col-md-7">
                                <span class="badge badge-primary" style="background: #2563eb; font-size: 11px; padding: 4px 8px; margin-bottom: 6px;" id="sum_prodi">PRODI</span>
                                <span class="badge badge-default" style="background: #e2e8f0; color:#334155; font-size: 11px; padding: 4px 8px; margin-bottom: 6px;" id="sum_tahun_kurikulum">Pustaka Kurikulum</span>
                                <h3 style="margin: 4px 0 6px 0; font-weight: 700; font-size: 17px; color: #0f172a;" id="sum_nama_mk">-</h3>
                                <div style="font-size: 12.5px; color: #64748b;" id="sum_sks_info">-</div>
                            </div>
                            <div class="col-md-5" style="border-left: 1px dashed #cbd5e1; font-size: 12px; color: #334155;">
                                <div><b style="color:#0f172a;">Dosen Pengampu:</b> <span id="sum_pengampu">-</span></div>
                                <div style="margin-top: 4px;"><b style="color:#0f172a;">Tgl Pembuatan:</b> <span id="sum_tgl_pembuatan">-</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Metric Cards -->
                    <div class="row" style="margin-bottom: 16px;">
                        <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; height:100%; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Kelengkapan Pertemuan</div>
                                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 4px;">
                                    <span style="font-size: 22px; font-weight: 800; color: #0f172a;" id="card_pertemuan_count">0/16</span>
                                    <span id="card_pertemuan_badge"></span>
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">Target standar 16 minggu/pertemuan</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6" style="margin-bottom: 10px;">
                            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; height:100%; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Bobot Asesmen</div>
                                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 4px;">
                                    <span style="font-size: 22px; font-weight: 800; color: #0f172a;" id="card_bobot_val">0%</span>
                                    <span id="card_bobot_badge"></span>
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">Wajib tepat bernilai 100%</div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12" style="margin-bottom: 10px;">
                            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px; height:100%; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Keselarasan CPL Kurikulum</div>
                                <div style="display: flex; align-items: baseline; justify-content: space-between; margin-top: 4px;">
                                    <span style="font-size: 22px; font-weight: 800; color: #0f172a;" id="card_cpl_pct">0%</span>
                                    <span id="card_cpl_badge"></span>
                                </div>
                                <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">Terkoneksi Pustaka Kurikulum</div>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluation Alert Callout -->
                    <div id="box_bobot_alert" style="border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;"></div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 14px; border-bottom: 2px solid #e2e8f0;">
                        <li role="presentation" class="active">
                            <a href="#tab_summary_matrix" aria-controls="tab_summary_matrix" role="tab" data-toggle="tab" style="font-weight:600; font-size:13px;">
                                <i class="fa fa-list mr-1"></i> Rincian 16 Pertemuan &amp; Bobot
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_summary_cpl" aria-controls="tab_summary_cpl" role="tab" data-toggle="tab" style="font-weight:600; font-size:13px;">
                                <i class="fa fa-book mr-1"></i> Profil &amp; Keselarasan CPL
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tab_summary_bloom" aria-controls="tab_summary_bloom" role="tab" data-toggle="tab" style="font-weight:600; font-size:13px;">
                                <i class="fa fa-sitemap mr-1"></i> Sebaran Taksonomi Bloom (KKO)
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content">
                        
                        <!-- TAB 1: Matriks 16 Pertemuan -->
                        <div role="tabpanel" class="tab-pane active" id="tab_summary_matrix">
                            <div class="row" style="margin-bottom: 12px;">
                                <div class="col-md-3 col-xs-6" style="margin-bottom: 6px;">
                                    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:8px 12px;">
                                        <div style="font-size:11px; color:#1e40af; font-weight:600;">Bobot Formatif</div>
                                        <div style="font-size:16px; font-weight:700; color:#1e3a8a; margin-top:2px;" id="sum_bobot_formatif">0%</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-6" style="margin-bottom: 6px;">
                                    <div style="background:#fefce8; border:1px solid #fef08a; border-radius:6px; padding:8px 12px;">
                                        <div style="font-size:11px; color:#854d0e; font-weight:600;">Bobot UTS (M8)</div>
                                        <div style="font-size:16px; font-weight:700; color:#713f12; margin-top:2px;" id="sum_bobot_uts">0%</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-6" style="margin-bottom: 6px;">
                                    <div style="background:#fdf2f8; border:1px solid #fbcfe8; border-radius:6px; padding:8px 12px;">
                                        <div style="font-size:11px; color:#9d174d; font-weight:600;">Bobot UAS (M16)</div>
                                        <div style="font-size:16px; font-weight:700; color:#831843; margin-top:2px;" id="sum_bobot_uas">0%</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-6" style="margin-bottom: 6px;">
                                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:8px 12px;">
                                        <div style="font-size:11px; color:#166534; font-weight:600;">Waktu Pembelajaran</div>
                                        <div style="font-size:13px; font-weight:700; color:#14532d; margin-top:2px;" id="sum_waktu_jam">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Meeting List Table -->
                            <div class="table-responsive" style="border: 1px solid #e2e8f0; border-radius: 6px; max-height: 280px; overflow-y: auto;">
                                <table class="table table-striped table-hover table-condensed" style="margin-bottom: 0; font-size: 12px;">
                                    <thead style="background: #f1f5f9; position: sticky; top: 0; z-index: 2;">
                                        <tr>
                                            <th style="width: 40px; text-align: center;">M</th>
                                            <th>Kemampuan Akhir / Sub-CPMK</th>
                                            <th>Bahan Kajian</th>
                                            <th>Metode</th>
                                            <th style="width: 70px; text-align: center;">Bobot</th>
                                            <th style="width: 90px; text-align: center;">Rubrik/Kriteria</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_matrix_eval"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: Keselarasan CPL Kurikulum -->
                        <div role="tabpanel" class="tab-pane" id="tab_summary_cpl">
                            <!-- Curriculum Document Source Box -->
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin-bottom: 14px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fa fa-file-pdf-o text-danger mr-1"></i> Sumber Dokumen Kurikulum
                                        </div>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; margin-top: 2px;" id="cpl_doc_name">-</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <span class="badge badge-primary" style="background:#2563eb; font-size: 11px; padding: 4px 8px;" id="cpl_doc_tahun">Kurikulum -</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary 2-Tile Row -->
                            <div class="row" style="margin-bottom: 14px;">
                                <div class="col-md-6 col-xs-12" style="margin-bottom: 8px;">
                                    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 14px 16px;">
                                        <div style="font-size: 11.5px; color: #1e40af; font-weight: 600;">CPL Terpetakan pada MK</div>
                                        <div style="font-size: 22px; font-weight: 800; color: #1e3a8a; margin-top: 2px;" id="cpl_count_mk">0 CPL</div>
                                        <div style="font-size: 11.5px; color: #3b82f6; margin-top: 2px;">Berdasarkan Matriks Kurikulum Resmi</div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12" style="margin-bottom: 8px;">
                                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 14px 16px;">
                                        <div style="font-size: 11.5px; color: #166534; font-weight: 600;">Terakomodasi di Dokumen RPS</div>
                                        <div style="font-size: 22px; font-weight: 800; color: #14532d; margin-top: 2px;" id="cpl_count_match">0 CPL</div>
                                        <div style="font-size: 11.5px; color: #16a34a; margin-top: 2px;" id="cpl_match_desc">Kesesuaian Narasi RPS</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary Description -->
                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; font-size: 12.5px; color: #334155; line-height: 1.5; margin-bottom: 12px;">
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">
                                    <i class="fa fa-info-circle text-primary mr-1"></i> Ringkasan Pemetaan CPL Mata Kuliah
                                </div>
                                <p id="cpl_narrative_text" style="margin-bottom: 0;">
                                    Menganalisis keterkaitan mata kuliah dengan dokumen kurikulum program studi...
                                </p>
                            </div>

                            <!-- CPL vs CPMK Evaluation Alert Box -->
                            <div id="box_cpl_cpmk_alert"></div>
                        </div>

                        <!-- TAB 3: Taksonomi Bloom -->
                        <div role="tabpanel" class="tab-pane" id="tab_summary_bloom">
                            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; margin-bottom: 6px;">
                                    <i class="fa fa-sitemap text-warning mr-1"></i> Sebaran Level Kognitif Bloom (Sub-CPMK 16 Pertemuan &amp; CPMK)
                                </div>
                                <p style="font-size: 12px; color: #64748b; margin-bottom: 16px;">
                                    Analisis Kata Kerja Operasional (KKO) yang teridentifikasi pada tahapan belajar mingguan (Sub-CPMK 16 Pertemuan) dan Capaian Pembelajaran Mata Kuliah (CPMK) sesuai standar SN-Dikti:
                                </p>
                                <div id="bloom_bars_container"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer" style="background: #fff; border-top: 1px solid #e2e8f0; padding: 12px 20px;">
                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" style="font-weight: 600; padding: 6px 16px;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function bukaSummaryRps(id_rps) {
    $('#modal_summary_rps').modal('show');
    $('#summary_loading').show();
    $('#summary_error').hide();
    $('#summary_content').hide();

    $.ajax({
        url: "<?= site_url('C_rps_new/ajax_summary_rps'); ?>",
        type: "POST",
        dataType: "JSON",
        data: {
            id_rps: id_rps,
            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
        },
        success: function(res) {
            $('#summary_loading').hide();
            if (!res.status) {
                $('#summary_error').html('<i class="fa fa-exclamation-triangle mr-1"></i> ' + res.message).show();
                return;
            }

            // Populate Header
            $('#sum_nama_mk').text('[' + res.kode_mk + '] ' + res.nm_mk);
            $('#sum_prodi').text(res.prodi_nama);
            $('#sum_tahun_kurikulum').text(res.tahun_kurikulum);
            $('#sum_sks_info').text('SKS MK: ' + res.sks_mk + ' (TM: ' + res.sks_tm + ', Prak: ' + res.sks_prak + ')');
            $('#sum_pengampu').text(res.pengampu ? res.pengampu : '-');
            $('#sum_tgl_pembuatan').text(res.tgl_pembuatan);

            // Scorecards
            $('#card_pertemuan_count').text(res.count_pertemuan + '/16');
            if (res.count_pertemuan >= 16) {
                $('#card_pertemuan_badge').html('<span class="badge badge-success" style="background:#10b981; font-size:10px; padding:2px 6px;">Lengkap 16 M</span>');
            } else {
                $('#card_pertemuan_badge').html('<span class="badge badge-warning" style="background:#f59e0b; font-size:10px; padding:2px 6px;">Kurang (' + res.count_pertemuan + ' M)</span>');
            }

            $('#card_bobot_val').text(res.total_bobot + '%');
            if (res.bobot_status === 'VALID') {
                $('#card_bobot_badge').html('<span class="badge badge-success" style="background:#10b981; font-size:10px; padding:2px 6px;">Tepat 100%</span>');
            } else if (res.bobot_status === 'OVER') {
                $('#card_bobot_badge').html('<span class="badge badge-danger" style="background:#ef4444; font-size:10px; padding:2px 6px;">Kelebihan</span>');
            } else {
                $('#card_bobot_badge').html('<span class="badge badge-warning" style="background:#f59e0b; font-size:10px; padding:2px 6px;">Kurang</span>');
            }

            $('#card_cpl_pct').text(res.cpl_alignment_pct + '%');
            if (res.total_cpl_mk > 0) {
                $('#card_cpl_badge').html('<span class="badge badge-primary" style="background:#2563eb; font-size:10px; padding:2px 6px;">' + res.total_cpl_mk + ' CPL Terpetakan</span>');
            } else {
                $('#card_cpl_badge').html('<span class="badge badge-warning" style="background:#f59e0b; font-size:10px; padding:2px 6px;">' + res.total_cpl_prodi + ' CPL Prodi</span>');
            }

            // Alert Box
            var alertBg = (res.bobot_status === 'VALID') ? '#f0fdf4' : ((res.bobot_status === 'OVER') ? '#fef2f2' : '#fffbeb');
            var alertBorder = (res.bobot_status === 'VALID') ? '#bbf7d0' : ((res.bobot_status === 'OVER') ? '#fecaca' : '#fde68a');
            var alertIcon = (res.bobot_status === 'VALID') ? '<i class="fa fa-check-circle text-success" style="font-size:16px; margin-right:8px;"></i>' : '<i class="fa fa-exclamation-circle text-danger" style="font-size:16px; margin-right:8px;"></i>';
            
            $('#box_bobot_alert').css({
                'background': alertBg,
                'border': '1px solid ' + alertBorder
            }).html(
                '<div style="display:flex; align-items:flex-start;">' +
                    alertIcon +
                    '<div>' +
                        '<div style="font-weight:700; color:#0f172a; font-size:13.5px;">Evaluasi Bobot Penilaian 16 Pertemuan</div>' +
                        '<div style="font-size:12.5px; color:#334155; margin-top:2px;">' + res.bobot_msg + '</div>' +
                    '</div>' +
                '</div>'
            );

            // Tab 1: Breakdown
            $('#sum_bobot_formatif').text(res.bobot_formatif + '%');
            $('#sum_bobot_uts').text(res.bobot_uts + '%');
            $('#sum_bobot_uas').text(res.bobot_uas + '%');
            $('#sum_waktu_jam').text(Math.round(res.total_teori_minutes / 60) + ' Jam TM / ' + Math.round(res.total_praktek_minutes / 60) + ' Jam Prak');

            // Render Table Matrix
            var rowsHtml = '';
            if (res.matrix_evaluation && res.matrix_evaluation.length > 0) {
                $.each(res.matrix_evaluation, function(i, row) {
                    var isEvalRow = (row.minggu == 8 || row.minggu == 16);
                    var trBg = isEvalRow ? 'style="background:#fef3c7; font-weight:600;"' : '';
                    var rubrikBadge = row.has_eval ? '<span class="label label-success" style="font-size:10px;">Ada</span>' : (row.bobot > 0 ? '<span class="label label-warning" style="font-size:10px;">Belum</span>' : '<span class="text-muted">-</span>');
                    
                    rowsHtml += '<tr ' + trBg + '>' +
                        '<td style="text-align:center;">' + row.minggu + '</td>' +
                        '<td>' + (row.kemampuan ? row.kemampuan : '-') + '</td>' +
                        '<td>' + (row.bahan_kajian ? row.bahan_kajian : '-') + '</td>' +
                        '<td>' + (row.metode ? row.metode : '-') + '</td>' +
                        '<td style="text-align:center; font-weight:700;">' + row.bobot + '%</td>' +
                        '<td style="text-align:center;">' + rubrikBadge + '</td>' +
                    '</tr>';
                });
            } else {
                rowsHtml = '<tr><td colspan="6" class="text-center text-muted p-3">Rincian pertemuan belum diupload.</td></tr>';
            }
            $('#tbody_matrix_eval').html(rowsHtml);

            // Tab 2: CPL Mapping Summary
            $('#cpl_doc_name').text(res.nama_dokumen);
            $('#cpl_doc_tahun').text('Kurikulum ' + res.tahun_dokumen);
            $('#cpl_count_mk').text(res.total_cpl_mk + ' CPL');
            $('#cpl_count_match').text(res.cpl_match_count + ' / ' + res.total_cpl_mk + ' CPL');
            $('#cpl_match_desc').text(res.cpl_alignment_pct + '% Keselarasan Narasi RPS');

            var narrative = 'Berdasarkan dokumen resmi <b>' + res.nama_dokumen + ' (Tahun ' + res.tahun_dokumen + ')</b> Program Studi <b>' + res.prodi_nama + '</b>, mata kuliah <b>[' + res.kode_mk + '] ' + res.nm_mk + '</b> dibebankan sebanyak <b>' + res.total_cpl_mk + ' CPL</b>.';
            $('#cpl_narrative_text').html(narrative);

            // CPL vs CPMK Redundancy Evaluation
            var cplCpmkHtml = '';
            if (res.is_cpl_cpmk_duplicate) {
                cplCpmkHtml = '<div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 12px 14px;">' +
                    '<div style="display: flex; align-items: flex-start;">' +
                        '<i class="fa fa-exclamation-triangle text-warning" style="font-size: 16px; margin-right: 10px; margin-top: 2px;"></i>' +
                        '<div>' +
                            '<div style="font-weight: 700; color: #92400e; font-size: 13px;">Peringatan OBE: CPL &amp; CPMK Terdeteksi Duplikat (' + res.cpl_cpmk_similarity + '% Kemiripan)</div>' +
                            '<div style="font-size: 12px; color: #78350f; margin-top: 3px; line-height: 1.45;">' +
                                'Teks pada bagian <b>CPL yang Dibebankan</b> dan <b>Tujuan Belajar / CPMK</b> terindikasi sama persis (copy-paste). ' +
                                'Menurut standar OBE &amp; SN-Dikti, <b>CPL</b> adalah capaian makro tingkat prodi, sedangkan <b>CPMK</b> harus dirumuskan secara operasional dan kontekstual spesifik untuk mata kuliah ini.' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            } else {
                cplCpmkHtml = '<div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px 14px;">' +
                    '<div style="display: flex; align-items: flex-start;">' +
                        '<i class="fa fa-check-circle text-success" style="font-size: 16px; margin-right: 10px; margin-top: 2px;"></i>' +
                        '<div>' +
                            '<div style="font-weight: 700; color: #166534; font-size: 13px;">Diferensiasi CPL &amp; CPMK Sesuai Standar OBE</div>' +
                            '<div style="font-size: 12px; color: #15803d; margin-top: 3px; line-height: 1.45;">' +
                                'Rumusan CPL dan CPMK telah terdiferensiasi dengan baik (Tingkat kemiripan teks hanya ' + res.cpl_cpmk_similarity + '%). CPMK telah dijabarkan sebagai kompetensi turunan yang spesifik bagi mata kuliah.' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }
            $('#box_cpl_cpmk_alert').html(cplCpmkHtml);

            // Tab 3: Bloom Bars (Format: Jumlah KKO)
            var bloomHtml = '';
            var baseMeetings = (res.count_pertemuan > 0) ? res.count_pertemuan : 16;
            $.each(res.bloom, function(k, b) {
                var badgeStyle = b.count > 0 
                    ? 'background:' + b.color + '; color:#fff; font-weight:600; font-size:11px; padding:3px 8px; border-radius:4px;'
                    : 'background:#f1f5f9; color:#94a3b8; font-weight:500; font-size:11px; padding:3px 8px; border-radius:4px; border:1px solid #e2e8f0;';
                var badgeText = b.count + ' KKO';
                var barWidth = b.count > 0 ? Math.min(100, Math.max(6, Math.round((b.count / baseMeetings) * 100))) : 0;

                bloomHtml += '<div style="margin-bottom:14px;">' +
                    '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">' +
                        '<span style="font-size:12.5px; font-weight:600; color:#1e293b;">' + b.label + '</span>' +
                        '<span class="badge" style="' + badgeStyle + '">' + badgeText + '</span>' +
                    '</div>' +
                    '<div class="progress" style="height:8px; margin-bottom:0; background:#f1f5f9; border-radius:4px; box-shadow:none; overflow:hidden;">' +
                        '<div class="progress-bar" style="width:' + barWidth + '%; background:' + b.color + '; border-radius:4px;"></div>' +
                    '</div>' +
                '</div>';
            });
            $('#bloom_bars_container').html(bloomHtml);

            $('#summary_content').fadeIn(200);
        },
        error: function() {
            $('#summary_loading').hide();
            $('#summary_error').html('<i class="fa fa-times-circle mr-1"></i> Terjadi kesalahan jaringan saat mengambil ringkasan RPS.').show();
        }
    });
}
</script>
