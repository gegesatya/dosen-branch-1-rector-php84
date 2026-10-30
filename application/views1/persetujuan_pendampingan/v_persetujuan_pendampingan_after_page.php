<script src="<?= base_url(); ?>assets/ignited_datatables/sweetalert2/sweetalert2.all.min.js"></script>
<script type="text/javascript">
	var table;
	var csrf_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
	var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

	$(document).ready(function() {
		table = $('#table').DataTable({
			"processing": true,
			"serverSide": true,
			"ordering": false,
			"ajax": {
				"url": "<?php echo site_url('C_persetujuan_pendampingan/ajax_list') ?>",
				"type": "POST",
				"data": function ( d ) {
					d[csrf_name] = csrf_hash;
				},
				"dataSrc": function ( json ) {
					// Update CSRF token from response
					if (json.csrf_hash) {
						csrf_hash = json.csrf_hash;
					}
					return json.data;
				}
			},
		});
	});

	function reload_table() {
		table.ajax.reload(null, false); //reload datatable ajax 
	}

	function change_status(id, status) {
		var action_text = status == 1 ? 'menyetujui' : 'membatalkan persetujuan';
		var title_text = status == 1 ? 'Setujui Pendampingan?' : 'Batalkan Persetujuan?';
		var type_icon = status == 1 ? 'question' : 'warning';
		var confirm_color = status == 1 ? '#28a745' : '#d33';
		var btn_text = status == 1 ? 'Ya, Setujui!' : 'Ya, Batalkan!';

		Swal({
			title: title_text,
			text: "Apakah Anda yakin ingin " + action_text + " data pendampingan ini?",
			type: type_icon,
			showCancelButton: true,
			confirmButtonColor: confirm_color,
			cancelButtonColor: '#6c757d',
			confirmButtonText: btn_text,
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.value) {
				var post_data = {
					'id_pendampingan': id,
					'status': status
				};
				post_data[csrf_name] = csrf_hash;

				$.ajax({
					url: "<?php echo site_url('C_persetujuan_pendampingan/change_status') ?>",
					type: "POST",
					data: post_data,
					dataType: "JSON",
					success: function(data) {
						// Update CSRF token
						if (data.csrf_hash) {
							csrf_hash = data.csrf_hash;
						}
						
						if (data.status) {
							reload_table();
							var success_msg = status == 1 ? 'Data berhasil disetujui!' : 'Persetujuan berhasil dibatalkan!';
							Swal({
								title: 'Sukses',
								text: success_msg,
								type: 'success'
							});
						} else {
							Swal({
								title: 'Gagal!',
								text: data.message || 'Gagal mengubah status.',
								type: 'error'
							});
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						Swal({
							title: 'Gagal!',
							text: 'Error mengubah status persetujuan.',
							type: 'error'
						});
					}
				});
			}
		});
	}

	function show_pdf(url, kategori, id_smt, nm_ptk, id_pendampingan, jumlah, approve_status) {
		$('#pdf_frame').attr('src', url);
		$('#modal_pdf .modal-title').text('SK ' + kategori + ' ' + id_smt);
		$('#pdf_subtitle').text('Dosen Pengaju: ' + nm_ptk);
		$('#pdf_handle_info').text('Jumlah (Mhs/Tim): ' + jumlah);

		if (approve_status == 0) {
			$('#btn_modal_approve').show();
			$('#btn_modal_approve').attr('onclick', 'modal_approve(' + id_pendampingan + ')');
		} else {
			$('#btn_modal_approve').hide();
		}

		$('#modal_pdf').modal('show');
	}

	function modal_approve(id) {
		$('#modal_pdf').modal('hide');
		change_status(id, 1);
	}
</script>
