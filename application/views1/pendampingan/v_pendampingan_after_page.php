<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
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
			$(this).parent().parent().removeClass('has-error');
			$(this).next().empty();
		});
		$("select").change(function() {
			$(this).parent().parent().removeClass('has-error');
			$(this).next().empty();
		});

	});

	function add_data() {
		save_method = 'add';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();
		$('#pdf_preview').hide();
		$('#modal_form').modal('show');
		$('#modal_form .modal-title').text('Tambah Data Pendampingan');
	}

	function edit_data(id) {
		save_method = 'update';
		$('#form')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();
		$('#pdf_preview').hide();

		$.ajax({
			url: "<?php echo site_url('C_pendampingan/ajax_edit/') ?>/" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				$('[name="id_pendampingan"]').val(data.id_pendampingan);
				$('[name="id_smt"]').val(data.id_smt);
				$('[name="kategori"]').val(data.kategori);
				$('[name="jumlah"]').val(data.jumlah);

				if(data.pdf_surat_keterangan) {
					$('#pdf_preview').show();
					$('#link_pdf').attr('href', '<?= base_url('uploads/pendampingan/') ?>' + data.pdf_surat_keterangan);
					$('[name="remove_pdf"]').val(data.pdf_surat_keterangan);
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
						$('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
						$('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]);
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
</script>
