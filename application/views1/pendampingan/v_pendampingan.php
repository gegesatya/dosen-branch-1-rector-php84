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
						<button class="btn btn-sm btn-success" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> Tambah Data</button>
						<button class="btn btn-sm btn-default" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Refresh</button>
					</div>
				</div>
				<div class="portlet-body">
					<table id="table" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th width="30">No</th>
								<th>Semester</th>
								<th>Kategori</th>
								<th>Jumlah Mahasiswa/Tim</th>
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
				<h3 class="modal-title">Form Pendampingan</h3>
			</div>
			<div class="modal-body form">
				<form action="#" id="form" class="form-horizontal">
					<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
					<input type="hidden" value="" name="id_pendampingan" />
					<div class="form-body">
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
									<option value="KKN">KKN</option>
									<option value="PKL">PKL</option>
									<option value="Fasilitator Entre">Fasilitator Entre</option>
									<option value="Pendamping UKM">Pendamping UKM</option>
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
						<div class="form-group" id="pdf-upload-group">
							<label class="control-label col-md-3">Surat Keterangan (PDF)</label>
							<div class="col-md-9">
								<input name="pdf_surat_keterangan" type="file" class="form-control" accept=".pdf">
								<span class="help-block">Maksimal 3MB</span>
								<div id="pdf_preview" style="display:none; margin-top:10px;">
									<a href="" target="_blank" class="btn btn-sm btn-info" id="link_pdf">Lihat File Saat Ini</a>
									<label><input type="checkbox" name="remove_pdf" value="1"> Hapus File Ini</label>
								</div>
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


<div class="modal fade" id="modal_pdf" role="dialog">
	<div class="modal-dialog modal-lg" style="width: 90%; max-width: 1200px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Dokumen Surat Keterangan</h3>
			</div>
			<div class="modal-body" style="padding:0;">
				<iframe src="" id="pdf_frame" style="width:100%; height:550px; border:none;"></iframe>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
