<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
<style>
.swal2-container {
  z-index: 99999 !important;
}
</style>
<script type="text/javascript">
	var save_method; //for save method string
	var table;

	$(document).ready(function() {

		table = $('#table').DataTable({
			"processing": true,
			"serverSide": true,
			"ordering": false,
			"ajax": {
				"url": "<?php echo site_url('C_fasilitator/ajax_list') ?>",
				"type": "POST",
				"data": function ( data ) {
					data['<?php echo $this->security->get_csrf_token_name(); ?>'] = '<?php echo $this->security->get_csrf_hash(); ?>';
				}
			},
		});

		$("input").change(function() {
			$(this).closest('.form-group').removeClass('has-error');
			$(this).closest('.form-group').find('.help-block').empty();
		});
		$("select").change(function() {
			$(this).closest('.form-group').removeClass('has-error');
			$(this).closest('.form-group').find('.help-block').empty();
		});

	});

	function add_data() {
		save_method = 'add';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();
		$('[name="id_smt"]').attr('disabled', false);
		$('[name="id_ptk"]').attr('disabled', false);
		$('[name="jumlah"]').attr('disabled', false).attr('readonly', false);
		$('[name="pdf_surat_keterangan"]').attr('disabled', false);
        
        $('#form-group-smt').show();
        $('#form-group-ptk').show();
        $('#form-group-link').show();

		$('#btnSave').attr('disabled', false);
		$('#modal_form').modal('show');
		$('#modal_form .modal-title').text('Tambah Data Fasilitator Manual');
	}

	function edit_data(id) {
		save_method = 'update';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();

		$('[name="id_smt"]').attr('disabled', true);
		$('[name="id_ptk"]').attr('disabled', true);
		$('[name="jumlah"]').attr('disabled', false);
        $('[name="pdf_surat_keterangan"]').attr('disabled', true);

        $('#form-group-smt').hide();
        $('#form-group-ptk').hide();
        $('#form-group-link').hide();

		$('#btnSave').attr('disabled', false);

		$.ajax({
			url: "<?php echo site_url('C_fasilitator/ajax_edit/') ?>/" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				$('[name="id_pendampingan"]').val(data.id_pendampingan);
				$('[name="jumlah"]').val(data.jumlah);

				$('#modal_form').modal('show');
				$('#modal_form .modal-title').text('Edit Jumlah Tim Fasilitator');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error get data from ajax');
			}
		});
	}

	function reload_table() {
		table.ajax.reload(null, false); //reload datatable ajax 
	}

	function save() {
		$('#btnSave').text('Menyimpan...');
		$('#btnSave').attr('disabled', true);
		var url;

		if (save_method == 'add') {
			url = "<?php echo site_url('C_fasilitator/ajax_add') ?>";
		} else {
			url = "<?php echo site_url('C_fasilitator/ajax_update') ?>";
		}

		var formData = new FormData($('#form')[0]);

		$.ajax({
			url: url,
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,
			dataType: "JSON",
			success: function(data) {
				if (data.status) {
					$('#modal_form').modal('hide');
					reload_table();
					var msg = save_method == 'add' ? 'Data berhasil disimpan!' : 'Data berhasil diperbarui!';
					Swal({
						title: 'Sukses',
						text: msg,
						type: 'success'
					});
				} else {
					for (var i = 0; i < data.inputerror.length; i++) {
						var elem = $('[name="' + data.inputerror[i] + '"]');
						elem.closest('.form-group').addClass('has-error');
						elem.closest('.form-group').find('.help-block').text(data.error_string[i]);
					}
				}
				$('#btnSave').text('Simpan');
				$('#btnSave').attr('disabled', false);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				Swal({
					title: 'Gagal!',
					text: 'Gagal memproses data.',
					type: 'error'
				});
				$('#btnSave').text('Simpan');
				$('#btnSave').attr('disabled', false);
			}
		});
	}

	function delete_data(id) {
		Swal({
			title: 'PERINGATAN SANGAT KETAT!',
			text: "Apakah Anda benar-benar yakin ingin menghapus data Fasilitator ini? Aksi ini TIDAK BISA DIBATALKAN dan data akan TERHAPUS PERMANEN!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Ya, HAPUS PERMANEN!',
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.value) {
				$.ajax({
					url: "<?php echo site_url('C_fasilitator/ajax_delete') ?>/" + id,
					type: "POST",
					data: {
						'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
					},
					dataType: "JSON",
					success: function(data) {
						reload_table();
						Swal({
							title: 'Dihapus!',
							text: 'Data fasilitator berhasil dihapus.',
							type: 'success'
						});
					},
					error: function(jqXHR, textStatus, errorThrown) {
						Swal({
							title: 'Gagal!',
							text: 'Gagal menghapus data.',
							type: 'error'
						});
					}
				});
			}
		});
	}

    function proses_fasilitator_ai() {
		var url_gdrive = $('#url_gdrive_fasilitator').val();
		if (url_gdrive == '') {
			Swal({
				title: 'Peringatan',
				text: 'Silakan isi Link Google Drive terlebih dahulu sebelum memproses SK.',
				type: 'warning'
			});
			return;
		}

		var btn = $('#btnProsesFasilitator');
		var originalText = btn.html();
		btn.html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
		btn.attr('disabled', true);

		$.ajax({
			url: "<?php echo site_url('C_fasilitator/ajax_proses_fasilitator_ai') ?>",
			type: "POST",
			data: {
				url_gdrive: url_gdrive,
				'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
			},
			dataType: "JSON",
			success: function(data) {
				btn.html(originalText);
				btn.attr('disabled', false);

				if (data.status) {
					$('#modal_fasilitator').modal('hide');
					reload_table();
					Swal({
						title: 'Sukses',
						text: data.msg,
						type: 'success'
					});
				} else {
					Swal({
						title: 'Gagal',
						text: data.msg || 'Terjadi kesalahan saat memproses data dengan AI.',
						type: 'error'
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				btn.html(originalText);
				btn.attr('disabled', false);
				var errMsg = errorThrown;
				if (jqXHR.responseText) {
					errMsg = jqXHR.responseText.substring(0, 150) + '...';
				}
				Swal({
					title: 'Error Server',
					text: 'Gagal menghubungi server. Status: ' + textStatus + '. Error: ' + errMsg,
					type: 'error'
				});
			}
		});
	}

	function proses_import_excel() {
		var fileInput = $('#file_excel')[0];
		if (!fileInput.files || fileInput.files.length === 0) {
			Swal({
				title: 'Peringatan',
				text: 'Silakan pilih file Excel terlebih dahulu.',
				type: 'warning'
			});
			return;
		}

		var btn = $('#btnProsesImport');
		var originalText = btn.html();
		btn.html('<i class="fa fa-spinner fa-spin"></i> Mengimpor...');
		btn.attr('disabled', true);

		var formData = new FormData($('#form_import_excel')[0]);

		$.ajax({
			url: "<?php echo site_url('C_fasilitator/ajax_import_excel') ?>",
			type: "POST",
			data: formData,
			contentType: false,
			processData: false,
			dataType: "JSON",
			success: function(data) {
				btn.html(originalText);
				btn.attr('disabled', false);

				if (data.status) {
					reload_table();
					$('#import_result_container').show();
					$('#import_alert_text').html('<b>' + data.msg + '</b>');

					// Render result table
					var html = '';
					if (data.details && data.details.length > 0) {
						for (var i = 0; i < data.details.length; i++) {
							var item = data.details[i];
							var badge = '';
							if (item.status === 'inserted') {
								badge = '<span class="badge badge-success">Baru Disimpan</span>';
							} else if (item.status === 'updated') {
								badge = '<span class="badge badge-info">Diperbarui</span>';
							} else {
								badge = '<span class="badge badge-danger">Gagal Cocok</span>';
							}

							html += '<tr>';
							html += '<td>' + (i + 1) + '</td>';
							html += '<td><b>' + item.nama + '</b></td>';
							html += '<td>' + (item.matched_to ? item.matched_to : '<i class="text-danger">Tidak Ditemukan</i>') + '</td>';
							html += '<td>' + (item.id_smt || '-') + '</td>';
							html += '<td>' + (item.jumlah !== undefined ? item.jumlah : '-') + '</td>';
							html += '<td>' + badge + '<br><small class="text-muted">' + (item.msg || '') + '</small></td>';
							html += '</tr>';
						}
					}
					$('#import_result_tbody').html(html);

					Swal({
						title: 'Selesai',
						text: data.msg,
						type: (data.failed_count > 0 ? 'warning' : 'success')
					});
				} else {
					Swal({
						title: 'Gagal',
						text: data.msg || 'Terjadi kesalahan saat mengimpor file Excel.',
						type: 'error'
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				btn.html(originalText);
				btn.attr('disabled', false);
				var errMsg = errorThrown;
				if (jqXHR.responseText) {
					errMsg = jqXHR.responseText.substring(0, 150) + '...';
				}
				Swal({
					title: 'Error Server',
					text: 'Gagal menghubungi server. Status: ' + textStatus + '. Error: ' + errMsg,
					type: 'error'
				});
			}
		});
	}

</script>
