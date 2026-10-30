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
				"url": "<?php echo site_url('C_pendampingan/ajax_list') ?>",
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
		$('[name="kategori"]').attr('disabled', false);
		$('[name="jumlah"]').attr('disabled', false).attr('readonly', false);
		$('#pdf_preview').hide();
		$('#ai_result_container').hide();
		$('#ai_dosen_container').hide();
		$('#ai_dosen_list_div').empty();
		$('#dosen_list_json').val('');
		$('#mahasiswa_json').val('');
		$('#form-dynamic-controls').hide();
		$('#btnSave').attr('disabled', true);
		$('#modal_form').modal('show');
		$('#modal_form .modal-title').text('Tambah Data Pendampingan');
	}

	function edit_data(id) {
		save_method = 'update';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();
		$('[name="id_smt"]').attr('disabled', true);
		$('[name="kategori"]').attr('disabled', true);
		$('[name="jumlah"]').attr('disabled', true);
		$('#btnSave').attr('disabled', false);
		$('#pdf_preview').hide();
		$('#ai_result_container').hide();
		$('#ai_dosen_container').hide();
		$('#ai_dosen_list_div').empty();
		$('#dosen_list_json').val('');
		$('#form-dynamic-controls').show();

		$.ajax({
			url: "<?php echo site_url('C_pendampingan/ajax_edit/') ?>/" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				$('[name="id_pendampingan"]').val(data.id_pendampingan);
				$('[name="id_smt"]').val(data.id_smt);
				$('[name="kategori"]').val(data.kategori);
				$('[name="jumlah"]').val(data.jumlah);
				$('[name="mahasiswa_json"]').val(data.mahasiswa_json || '');

				if(data.pdf_surat_keterangan) {
					$('#pdf_preview').show();
					$('#link_pdf').attr('href', data.pdf_surat_keterangan);
					$('[name="pdf_surat_keterangan"]').val(data.pdf_surat_keterangan);
				}

				$('#modal_form').modal('show');
				$('#modal_form .modal-title').text('Edit Data Pendampingan');
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
			url = "<?php echo site_url('C_pendampingan/ajax_add') ?>";
		} else {
			url = "<?php echo site_url('C_pendampingan/ajax_update') ?>";
		}

		$('[name="id_smt"]').attr('disabled', false);
		$('[name="kategori"]').attr('disabled', false);
		$('[name="jumlah"]').attr('disabled', false);

		var formData = new FormData($('#form')[0]);

		$('[name="id_smt"]').attr('disabled', true);
		$('[name="kategori"]').attr('disabled', true);
		$('[name="jumlah"]').attr('disabled', true);

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
					var msg = data.msg || (save_method == 'add' ? 'Data berhasil disimpan!' : 'Data berhasil diperbarui!');
					Swal({
						title: 'Sukses',
						text: msg,
						type: 'success'
					});
				} else {
					if (data.inputerror) {
						for (var i = 0; i < data.inputerror.length; i++) {
							var elem = $('[name="' + data.inputerror[i] + '"]');
							elem.closest('.form-group').addClass('has-error');
							elem.closest('.form-group').find('.help-block').html(data.error_string[i]);
						}
					}
					Swal({
						title: 'Gagal',
						text: data.msg || 'Periksa kembali isian form Anda.',
						type: 'error'
					});
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
			title: 'Apakah Anda yakin?',
			text: "Data pendampingan ini akan dihapus permanen beserta filenya!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Ya, hapus!',
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.value) {
				$.ajax({
					url: "<?php echo site_url('C_pendampingan/ajax_delete') ?>/" + id,
					type: "POST",
					data: {
						'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
					},
					dataType: "JSON",
					success: function(data) {
						reload_table();
						Swal({
							title: 'Dihapus!',
							text: 'Data berhasil dihapus.',
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

	function show_pdf(url, kategori, id_smt) {
		$('#pdf_frame').attr('src', url);
		$('#modal_pdf .modal-title').text('SK ' + kategori + ' ' + id_smt);
		$('#modal_pdf').modal('show');
	}

	function proses_sk_ai() {
		var url_gdrive = $('#pdf_surat_keterangan').val();
		if (url_gdrive == '') {
			Swal({
				title: 'Peringatan',
				text: 'Silakan isi Link Google Drive terlebih dahulu sebelum memproses SK.',
				type: 'warning'
			});
			return;
		}

		var btn = $('#btnProsesAI');
		var originalText = btn.html();
		btn.html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
		btn.attr('disabled', true);
		$('#ai_result_container').hide();
		$('#ai_dosen_container').hide();
		$('#ai_dosen_list_div').empty();
		$('#dosen_list_json').val('');

		$.ajax({
			url: "<?php echo site_url('C_pendampingan/ajax_proses_sk_ai') ?>",
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
					// Set Semester & Kunci
					if (data.id_smt) {
						if ($('[name="id_smt"] option[value="'+data.id_smt+'"]').length == 0) {
							var text = data.semester_teks ? data.semester_teks : data.id_smt;
							$('[name="id_smt"]').append('<option value="'+data.id_smt+'">'+text+'</option>');
						}
						$('[name="id_smt"]').val(data.id_smt);
					} else if (data.semester_teks) {
						Swal({
							title: 'Perhatian',
							text: 'Semester "' + data.semester_teks + '" ditemukan di dokumen, tetapi sistem tidak dapat mencocokkannya secara otomatis. Silakan pilih manual.',
							type: 'warning'
						});
					}

					// Set Kategori
					if (data.kategori) {
						$('[name="kategori"]').val(data.kategori);
					}
					
					// Set Jumlah Total
					if (data.total_mahasiswa) {
						$('[name="jumlah"]').val(data.total_mahasiswa);
						$('[name="jumlah"]').attr('readonly', true);
					}

					// Alert Validasi
					$('#ai_result_container').show();
					var alertBox = $('#ai_alert_box');
					alertBox.removeClass('alert-info alert-success alert-danger alert-warning');
					
					if (data.is_valid && data.dosen_list && data.dosen_list.length > 0) {
						$('#form-dynamic-controls').show();
						$('[name="id_smt"]').attr('disabled', true);
						$('[name="kategori"]').attr('disabled', true);
						$('[name="jumlah"]').attr('disabled', true);
						$('#btnSave').attr('disabled', false);

						alertBox.addClass('alert-success');
						var dCount = data.total_dosen || data.dosen_list.length;
						var mCount = data.total_mahasiswa || 0;
						$('#ai_alert_text').html('<i class="fa fa-check-circle"></i> <b>SK Valid!</b> Ditemukan <b>' + dCount + ' Dosen Pembimbing</b> dengan total <b>' + mCount + ' Mahasiswa</b>.');

						// Render Daftar Dosen & Mahasiswa
						var html = '';
						for (var i = 0; i < data.dosen_list.length; i++) {
							var d = data.dosen_list[i];
							var matchBadge = d.matched ? '<span class="badge badge-success" style="font-size:11px;"><i class="fa fa-check"></i> ' + d.nm_ptk + '</span>' : '<span class="badge badge-danger" style="font-size:11px;"><i class="fa fa-warning"></i> Tidak cocok di DB</span>';
							var mhsCount = d.mahasiswa ? d.mahasiswa.length : 0;
							
							html += '<div class="panel panel-default" style="margin-bottom: 8px; border: 1px solid #e0e0e0;">';
							html += '  <div class="panel-heading" style="padding: 7px 10px; background:#f9f9f9; display:flex; justify-content:space-between; align-items:center;">';
							html += '    <div><b>' + (i+1) + '. ' + d.nama_dosen + '</b> &nbsp;' + matchBadge + '</div>';
							html += '    <span class="badge badge-info" style="font-size:11px;">' + mhsCount + ' Mahasiswa</span>';
							html += '  </div>';
							if (d.mahasiswa && d.mahasiswa.length > 0) {
								html += '  <div class="panel-body" style="padding: 5px 10px; max-height: 130px; overflow-y:auto; font-size:12px;">';
								html += '    <table class="table table-condensed table-striped" style="margin-bottom:0; font-size:11px;">';
								html += '      <thead><tr><th width="30">No</th><th width="120">NIM</th><th>Nama Mahasiswa</th></tr></thead>';
								html += '      <tbody>';
								for (var j=0; j<d.mahasiswa.length; j++) {
									var m = d.mahasiswa[j];
									html += '<tr><td>' + (j+1) + '</td><td><b>' + (m.nim || '-') + '</b></td><td>' + (m.nama || '-') + '</td></tr>';
								}
								html += '      </tbody>';
								html += '    </table>';
								html += '  </div>';
							}
							html += '</div>';
						}
						$('#ai_dosen_list_div').html(html);
						$('#ai_dosen_container').show();
						$('#dosen_list_json').val(JSON.stringify(data.dosen_list));
					} else {
						$('#form-dynamic-controls').hide();
						$('#btnSave').attr('disabled', true);
						alertBox.addClass('alert-warning');
						$('#ai_alert_text').html('<i class="fa fa-exclamation-triangle"></i> <b>Perhatian:</b> ' + (data.reason || 'Tidak ada dosen bimbingan terdeteksi di dokumen.'));
						$('#ai_dosen_container').hide();
						$('#dosen_list_json').val('');
					}

					Swal({
						title: 'Selesai',
						text: 'Proses AI berhasil mengekstrak seluruh data dosen pembimbing dan mahasiswa dari SK.',
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

	function show_mahasiswa_from_btn(btn) {
		var json_str = $(btn).attr('data-mhs') || '[]';
		show_mahasiswa(json_str);
	}

	function show_mahasiswa(json_str) {
		try {
			var mahasiswa = (typeof json_str === 'object') ? json_str : JSON.parse(json_str);
			var html = '';
			if (mahasiswa && mahasiswa.length > 0) {
				for (var i = 0; i < mahasiswa.length; i++) {
					var item = mahasiswa[i];
					var nim = item.nim || item.nipd || '-';
					var nama = item.nama || item.nm_pd || '-';
					html += '<tr>';
					html += '<td>' + (i + 1) + '</td>';
					html += '<td>' + nim + '</td>';
					html += '<td>' + nama + '</td>';
					html += '</tr>';
				}
			} else {
				html += '<tr><td colspan="3" class="text-center">Tidak ada data mahasiswa.</td></tr>';
			}
			$('#list_mahasiswa_tbody').html(html);
			$('#modal_mahasiswa').modal('show');
		} catch (e) {
			console.error("Error parsing mahasiswa data:", e);
			alert("Gagal menampilkan data mahasiswa: " + e.message);
		}
	}
</script>
