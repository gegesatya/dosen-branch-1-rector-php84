<div class="page-content" style="padding-top:0">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light bordered">
				<div class="portlet-title">
					<div class="caption">
						<i class="icon-docs font-blue-hoki"></i>
						<span class="caption-subject font-blue-hoki bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
					</div>
					<div class="actions">
						<button class="btn btn-sm btn-warning" onclick="$('#modal_import_excel').modal('show')"><i class="fa fa-file-excel-o"></i> Impor Excel</button>
                        <button class="btn btn-sm btn-info" onclick="$('#modal_fasilitator').modal('show')"><i class="glyphicon glyphicon-plus"></i> Tambah Fasilitator via AI</button>
						<button class="btn btn-sm btn-success" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> Tambah Manual</button>
						<button class="btn btn-sm btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Refresh</button>
					</div>
				</div>
				<div class="portlet-body">
					<table id="table" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th width="30">No</th>
                                <th>Nama Dosen</th>
								<th>Semester</th>
								<th>Jumlah Tim</th>
								<th>Status</th>
								<th>Surat Keterangan</th>
								<th style="width:100px;">Aksi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_form" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Form Data Fasilitator</h3>
			</div>
			<div class="modal-body form">
				<form action="#" id="form" class="form-horizontal">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
					<input type="hidden" value="" name="id_pendampingan" />
					<div class="form-body">
						
                        <div class="form-group" id="form-group-smt">
                            <label class="control-label col-md-3">Semester</label>
                            <div class="col-md-9">
                                <select name="id_smt" class="form-control">
                                    <option value="">-- Pilih Semester --</option>
                                    <?php foreach ($dd_semester as $smt) : ?>
                                        <option value="<?= $smt->id_smt; ?>"><?= $smt->nm_smt; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group" id="form-group-ptk">
                            <label class="control-label col-md-3">Dosen</label>
                            <div class="col-md-9">
                                <select name="id_ptk" class="form-control select2" style="width:100%;">
                                    <option value="">-- Pilih Dosen --</option>
                                    <?php foreach ($dd_dosen as $d) : ?>
                                        <option value="<?= $d->id_ptk; ?>"><?= $d->nm_ptk; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3">Jumlah (Tim)</label>
                            <div class="col-md-9">
                                <input name="jumlah" placeholder="Jumlah" class="form-control" type="number" min="0">
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group" id="form-group-link">
                            <label class="control-label col-md-3">Link SK (G-Drive)</label>
                            <div class="col-md-9">
                                <input name="pdf_surat_keterangan" type="text" class="form-control" placeholder="Masukkan Link Google Drive">
                                <span class="help-block text-muted" style="font-size:12px;">Pastikan akses link "Anyone with the link can view"</span>
                            </div>
                        </div>

					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				<button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Simpan</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_fasilitator" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Tambah Fasilitator via AI</h3>
			</div>
			<div class="modal-body form">
				<form id="form_fasilitator" class="form-horizontal">
					<div class="form-body">
						<div class="form-group">
							<label class="control-label col-md-3">Link SK (G-Drive)</label>
							<div class="col-md-9">
								<input name="url_gdrive_fasilitator" id="url_gdrive_fasilitator" type="text" class="form-control" placeholder="Masukkan Link Google Drive">
								<span class="help-block text-muted" style="font-size:12px;">Pastikan akses link "Anyone with the link can view"</span>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				<button type="button" id="btnProsesFasilitator" onclick="proses_fasilitator_ai()" class="btn btn-primary">🤖 Proses & Simpan</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Impor Excel -->
<div class="modal fade" id="modal_import_excel" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title"><i class="fa fa-file-excel-o"></i> Impor Data Fasilitator via Excel</h3>
			</div>
			<div class="modal-body form">
				<form id="form_import_excel" class="form-horizontal" enctype="multipart/form-data">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
					<div class="form-body">
						<div class="alert alert-info" style="font-size: 13px; margin-bottom: 15px;">
							<b>Format Kolom File Excel:</b>
							<ul style="margin-bottom: 0; padding-left: 20px;">
								<li><b>Kolom A (nm_ptk):</b> Nama Fasilitator / Dosen <i>(Akan dicocokkan ke database siakad_dosen)</i></li>
								<li><b>Kolom B (total):</b> Jumlah Total Tim <i>(Dimasukkan ke kolom jumlah)</i></li>
								<li><b>Kolom C (id_smt):</b> ID Semester <i>(Contoh: 20252. Jika kosong, akan memakai semester default di bawah)</i></li>
							</ul>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3">File Excel (.xlsx / .xls / .csv) <span class="text-danger">*</span></label>
							<div class="col-md-9">
								<input name="file_excel" id="file_excel" type="file" class="form-control" accept=".xlsx, .xls, .csv" required>
								<span class="help-block text-muted" style="font-size:12px;">Pilih file Excel yang berisi daftar fasilitator.</span>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3">Semester Default (Fallback)</label>
							<div class="col-md-9">
								<select name="id_smt_fallback" id="id_smt_fallback" class="form-control">
									<option value="">-- Pilih Semester (Jika di Excel kosong) --</option>
									<?php foreach ($dd_semester as $smt) : ?>
										<option value="<?= $smt->id_smt; ?>"><?= $smt->nm_smt; ?> (<?= $smt->id_smt; ?>)</option>
									<?php endforeach; ?>
								</select>
								<span class="help-block text-muted" style="font-size:12px;">Digunakan jika kolom id_smt di file Excel tidak terisi.</span>
							</div>
						</div>

						<!-- Container Hasil Impor -->
						<div id="import_result_container" style="display:none; margin-top: 15px;">
							<div id="import_alert_box" class="alert alert-success" style="margin-bottom: 10px; padding: 10px;">
								<span id="import_alert_text"></span>
							</div>
							<div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">
								<table class="table table-bordered table-striped table-condensed" style="margin-bottom:0; font-size:12px;">
									<thead>
										<tr>
											<th width="30">No</th>
											<th>Nama di Excel</th>
											<th>Nama Dosen Cocok</th>
											<th>Semester</th>
											<th>Jumlah</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody id="import_result_tbody">
									</tbody>
								</table>
							</div>
						</div>

					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
				<button type="button" id="btnProsesImport" onclick="proses_import_excel()" class="btn btn-success"><i class="fa fa-upload"></i> Unggah & Impor</button>
			</div>
		</div>
	</div>
</div>
