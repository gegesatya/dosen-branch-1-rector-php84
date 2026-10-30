<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembersihan Data Asal Sekolah</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; }
        .card { border-radius: 10px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); border: none; margin-bottom: 30px; }
        .card-header { background-color: #4e73df; color: white; border-top-left-radius: 10px !important; border-top-right-radius: 10px !important; font-weight: bold; }
        .table th { background-color: #f8f9fc; color: #4e73df; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h2 class="mb-4 text-gray-800">Standarisasi Data Asal Sekolah</h2>

        <?php if($this->session->flashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $this->session->flashdata('message') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Data Belum Dipetakan (Terdapat <?= count($unmapped_sekolah) ?> nama unik)</span>
                        <div>
                            <button class="btn btn-sm btn-warning text-dark fw-bold me-2" data-bs-toggle="modal" data-bs-target="#modalTambahReferensi">
                                + Tambah Referensi Manual
                            </button>
                            <a href="<?= site_url('C_cleansing_sekolah/auto_match_smart') ?>" class="btn btn-sm btn-light text-primary fw-bold" onclick="return confirm('Apakah Anda yakin ingin memetakan otomatis data yang sama persis?')">
                                Mulai Auto-Match (Pencocokan Persis)
                            </a>
                        </div>
                    </div>
                    
                    <!-- Modal Tambah Referensi -->
                    <div class="modal fade" id="modalTambahReferensi" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Tambah Referensi Sekolah Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <form id="formTambahReferensi">
                                <div class="mb-3">
                                    <label class="form-label">NPSN (Kosongkan jika tidak tahu)</label>
                                    <input type="text" class="form-control" name="npsn" placeholder="Contoh: 10210856">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Sekolah Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="sekolah" required placeholder="Contoh: MAS ABINNUR AL-ISLAMI">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kategori / Bentuk <span class="text-danger">*</span></label>
                                    <select class="form-select" name="bentuk">
                                        <option value="MA">MA (Madrasah Aliyah)</option>
                                        <option value="SMA">SMA</option>
                                        <option value="SMK">SMK</option>
                                        <option value="Pesantren">Pesantren / PKBM</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kabupaten / Kota</label>
                                    <input type="text" class="form-control" name="kabupaten_kota" placeholder="Contoh: Kab. Mandailing Natal">
                                </div>
                            </form>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="btnSimpanReferensi">Simpan Referensi</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="20%">Nama Asli di Database (Input Mahasiswa)</th>
                                        <th width="10%">Kategori</th>
                                        <th width="20%">Alamat Asli</th>
                                        <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                        <th width="25%">Cari Referensi (Dapodik Kemdikbud)</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($unmapped_sekolah)): ?>
                                        <tr><td colspan="7" class="text-center">Semua data telah berhasil dipetakan!</td></tr>
                                    <?php else: ?>
                                        <?php $no=1; foreach($unmapped_sekolah as $u): ?>
                                        <tr id="row-<?= $no ?>">
                                            <td class="text-center"><?= $no ?></td>
                                            <td>
                                                <span class="fw-bold"><?= $u->sekolah_asal ?></span>
                                                <input type="hidden" id="asal-<?= $no ?>" value="<?= htmlspecialchars($u->sekolah_asal) ?>">
                                            </td>
                                            <td class="small">
                                                <?= htmlspecialchars($u->kategori_sekolah) ?>
                                            </td>
                                            <td class="small text-muted">
                                                <?= htmlspecialchars($u->alamat_sekolah) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary rounded-pill px-3 py-2"><?= $u->jumlah_mahasiswa ?> org</span>
                                            </td>
                                            <td>
                                                <select class="form-select select2-referensi" id="ref-<?= $no ?>" data-placeholder="Ketik nama sekolah atau NPSN..."></select>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-success btn-simpan" data-id="<?= $no ?>">Simpan</button>
                                            </td>
                                        </tr>
                                        <?php $no++; endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success">
                        <span>Data Yang Sudah Dipetakan (Preview 100 Terbaru)</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Asli</th>
                                        <th>Nama Standar (Referensi)</th>
                                        <th>Jlh Mhs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($mapped_sekolah as $m): ?>
                                    <tr>
                                        <td><?= $m->sekolah_asal ?></td>
                                        <td class="text-success fw-bold"><?= $m->nama_referensi ?></td>
                                        <td><?= $m->jumlah_mahasiswa ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 with AJAX
            $('.select2-referensi').select2({
                theme: 'bootstrap-5',
                width: '100%',
                ajax: {
                    url: '<?= site_url("C_cleansing_sekolah/search_referensi") ?>',
                    dataType: 'json',
                    delay: 500, // delay 500ms to reduce database load
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.items };
                    },
                    cache: true
                },
                minimumInputLength: 3 // require at least 3 chars to start searching
            });

            // Handle Save Button
            $('.btn-simpan').click(function() {
                var rowId = $(this).data('id');
                var sekolahAsal = $('#asal-' + rowId).val();
                var idReferensi = $('#ref-' + rowId).val();

                if (!idReferensi) {
                    toastr.warning('Silakan pilih referensi sekolah terlebih dahulu!');
                    return;
                }

                var btn = $(this);
                btn.prop('disabled', true).text('Menyimpan...');

                var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
                var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
                
                var dataPayload = {
                    sekolah_asal: sekolahAsal,
                    id_referensi: idReferensi
                };
                dataPayload[csrfName] = csrfHash;

                $.ajax({
                    url: '<?= site_url("C_cleansing_sekolah/save_mapping") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: dataPayload,
                    success: function(response) {
                        if (response.status == 'success') {
                            toastr.success(response.message);
                            $('#row-' + rowId).fadeOut('slow', function() { $(this).remove(); });
                        } else {
                            toastr.error(response.message);
                            btn.prop('disabled', false).text('Simpan');
                        }
                    },
                    error: function() {
                        toastr.error('Terjadi kesalahan pada server.');
                        btn.prop('disabled', false).text('Simpan');
                    }
                });
            });

            // Handle Simpan Referensi Manual
            $('#btnSimpanReferensi').click(function() {
                var btn = $(this);
                var dataPayload = $('#formTambahReferensi').serializeArray();
                
                var csrfName = '<?= $this->security->get_csrf_token_name() ?>';
                var csrfHash = '<?= $this->security->get_csrf_hash() ?>';
                dataPayload.push({name: csrfName, value: csrfHash});

                btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: '<?= site_url("C_cleansing_sekolah/tambah_referensi") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: $.param(dataPayload),
                    success: function(response) {
                        if (response.status == 'success') {
                            toastr.success(response.message);
                            $('#modalTambahReferensi').modal('hide');
                            $('#formTambahReferensi')[0].reset();
                        } else {
                            toastr.error(response.message);
                        }
                        btn.prop('disabled', false).text('Simpan Referensi');
                    },
                    error: function() {
                        toastr.error('Terjadi kesalahan pada server.');
                        btn.prop('disabled', false).text('Simpan Referensi');
                    }
                });
            });
        });
    </script>
</body>
</html>
