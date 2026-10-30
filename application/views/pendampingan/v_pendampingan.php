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
						<?php if (!empty($is_wadir2)) : ?>
							<button class="btn btn-sm btn-success" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> Tambah Data</button>
						<?php endif; ?>
						<button class="btn btn-sm btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Refresh</button>
					</div>
				</div>
				<div class="portlet-body">
					<table id="table" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th width="30">No</th>
								<?php if (!empty($is_wadir2)) : ?>
									<th>Dosen</th>
								<?php endif; ?>
								<th>Semester</th>
								<th>Kategori</th>
								<th>Jumlah Mahasiswa/Tim</th>
								<th>Status</th>
								<th>Surat Keterangan</th>
								<?php if (!empty($is_wadir2)) : ?>
									<th style="width:100px;">Aksi</th>
								<?php endif; ?>
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
	<div class="modal-dialog" style="width: 750px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Form Pendampingan</h3>
			</div>
			<div class="modal-body form">
				<form action="#" id="form" class="form-horizontal">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
					<input type="hidden" value="" name="id_pendampingan" />
					<div class="form-body">
						<div class="form-group" id="pdf-upload-group">
							<label class="control-label col-md-3">Link Surat Keterangan (G-Drive)</label>
							<div class="col-md-9">
								<div class="input-group">
									<input name="pdf_surat_keterangan" id="pdf_surat_keterangan" type="text" class="form-control" placeholder="Masukkan Link Google Drive">
									<span class="input-group-btn">
										<button class="btn btn-primary" type="button" id="btnProsesAI" onclick="proses_sk_ai()">🤖 Proses SK via AI</button>
									</span>
								</div>
								<span class="help-block"></span>
								<span class="text-muted" style="display:block; margin-top:5px; font-size:12px;">Pastikan akses link diatur ke "Anyone with the link can view". File berupa PDF/DOC/DOCX.</span>
								<div id="pdf_preview" style="display:none; margin-top:10px;">
									<a href="" target="_blank" class="btn btn-sm btn-info" id="link_pdf">Buka Link Saat Ini</a>
								</div>
								
								<!-- Container Hasil AI -->
								<div id="ai_result_container" style="display:none; margin-top: 15px;">
									<div id="ai_alert_box" class="alert alert-info" style="margin-bottom: 10px; padding: 10px;">
										<i class="fa fa-info-circle"></i> <span id="ai_alert_text">Status Validasi SK akan muncul di sini.</span>
									</div>
									<div id="ai_dosen_container" style="display:none; max-height: 320px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fafafa; border-radius: 4px;">
										<h5 style="margin-top:0; font-weight:bold;"><i class="fa fa-list-alt"></i> Daftar Dosen Pembimbing & Mahasiswa Terdeteksi:</h5>
										<div id="ai_dosen_list_div"></div>
									</div>
								</div>
							</div>
						</div>
						<div id="form-dynamic-controls" style="display:none;">
							<div class="form-group">
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
						<div class="form-group">
							<label class="control-label col-md-3">Kategori</label>
							<div class="col-md-9">
								<select name="kategori" class="form-control">
									<option value="">-- Pilih Kategori --</option>
									<option value="KKN">Kuliah Kerja Nyata (KKN)</option>
									<option value="PKL">Praktek Kerja Lapangan/Internship/Magang</option>
								</select>
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Jumlah (Mhs/Tim)</label>
							<div class="col-md-9">
								<input name="jumlah" placeholder="Jumlah" class="form-control" type="number" min="1">
								<span class="help-block"></span>
							</div>
						</div>
						</div>
						<textarea name="mahasiswa_json" id="mahasiswa_json" style="display:none;"></textarea>
						<textarea name="dosen_list_json" id="dosen_list_json" style="display:none;"></textarea>

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


<div class="modal fade" id="modal_pdf" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				<h4 class="modal-title">PDF Preview</h4>
			</div>
			<div class="modal-body">
				<iframe id="pdf_frame" src="" width="100%" height="500px"></iframe>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_mahasiswa" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Daftar Mahasiswa Bimbingan</h3>
			</div>
			<div class="modal-body">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="30">No</th>
							<th>NIM</th>
							<th>Nama Mahasiswa</th>
						</tr>
					</thead>
					<tbody id="list_mahasiswa_tbody">
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
