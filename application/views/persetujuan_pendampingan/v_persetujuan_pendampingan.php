<div class="page-content" style="padding-top:0">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet light bordered">
				<div class="portlet-title">
					<div class="caption">
						<i class="icon-check font-blue-hoki"></i>
						<span class="caption-subject font-blue-hoki bold uppercase"><?= $judul_besar; ?> <small><?= $judul_kecil; ?></small></span>
					</div>
					<div class="actions">
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
								<th>Kategori</th>
								<th>Jumlah (Mhs/Tim)</th>
								<th>Surat Keterangan</th>
								<th>Status</th>
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


<div class="modal fade" id="modal_pdf" role="dialog">
	<div class="modal-dialog modal-lg" style="width: 90%; max-width: 1200px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Dokumen Surat Keterangan</h3>
				<span class="text-muted" id="pdf_subtitle" style="display:block; margin-top:5px; font-weight:normal; font-size:14px;"></span>
			</div>
			<div class="modal-body" style="padding:0;">
				<iframe src="" id="pdf_frame" style="width:100%; height:550px; border:none;"></iframe>
			</div>
			<div class="modal-footer">
				<div class="pull-left" style="padding-top:6px; font-weight:bold; font-size:14px; color:#333;" id="pdf_handle_info"></div>
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
				<button type="button" id="btn_modal_approve" class="btn btn-success"><i class="fa fa-check"></i> Setujui</button>
			</div>
		</div>
	</div>
</div>
