<script type="text/javascript">
	$(document).ready(function() {
		$('#kontrol').hide();
		$('#tabel_kehadiran').hide();

		$('select[name=ddKelasKuliah]').on('change', function() {
			dropdown_change($(this).val());
		});

		initialise();

		$('#form_submit').submit(function(e) {
			e.preventDefault();
			$.ajax({
				url: '<?php echo site_url('C_isi_nilai_excel_mbkm/save_excel/') ?>',
				type: "post",
				dataType: "JSON",
				data: new FormData(this),
				processData: false,
				contentType: false,
				cache: false,
				async: false,
				success: function(data) {
					if (data.tipe == 0) {
						$("#myalert").addClass("alert alert-success");
						$("#myalert").text("Sukses! " + data.message);
						dropdown_change(data.id_kls);
					} else {
						$("#myalert").addClass("alert alert-danger");
						$("#myalert").text("Error! " + data.message);
					}

					$('#userfile').val('');
				}
			});
		});

	});

	function dropdown_change(id_kls) {
		$my_var = $("select[name=ddKelasKuliah] option:selected").text();
		var dataku = $('#form').serializeArray();
		dataku.push({
			name: '<?php echo $this->security->get_csrf_token_name(); ?>',
			value: '<?php echo $this->security->get_csrf_hash(); ?>'
		});

		if (id_kls == '') {
			$('#kontrol').hide();
			$('#tabel_kehadiran').hide();
			$('#sub-caption').text('');
		} else {
			$.ajax({
				url: "<?php echo site_url('C_isi_nilai_excel_mbkm/save_id_kls_session/') ?>" + id_kls,
				type: "POST",
				data: dataku,
				dataType: "JSON",
				success: function(data) {
					$('#kontrol').show();
					$('#tabel_kehadiran').show();
					$('#sub-caption').text($my_var);
					$('#tabel_kehadiran').html(data.html_tabel);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert('Salah pilih kelas kuliah, silahkan pilih kelas yang tersedia');
				}
			});
		}
	}

	function initialise() {
		$.fn.editable.defaults.inputclass = 'form-control';
		$.fn.editable.defaults.url = '/post';

		$('.meet_1').editable({
			prepend: "NA",
			url: '<?php echo site_url(); ?>C_isi_nilai_excel_mbkm/save',
			inputclass: 'form-control',
			showbuttons: false,
			params: function(params) {
				params["<?php echo $this->security->get_csrf_token_name(); ?>"] = "<?php echo $this->security->get_csrf_hash(); ?>";
				return params;
			},
			source: [{
				value: 'Hadir',
				text: 'H'
			}, {
				value: 'Tidak Hadir',
				text: 'T'
			}]
		});

	};

	function sinkronisasi() {
		var dataku = $('#form').serializeArray();
		dataku.push({
			name: '<?php echo $this->security->get_csrf_token_name(); ?>',
			value: '<?php echo $this->security->get_csrf_hash(); ?>'
		});

		$.ajax({
			url: "<?php echo site_url('C_isi_nilai_excel_mbkm/sinkronisasi/') ?>",
			type: "POST",
			data: dataku,
			dataType: "JSON",
			success: function(data) {
				$my_var = $("select[name=ddKelasKuliah] option:selected").val();
				dropdown_change($my_var);
			},
			error: function(jqXHR, textStatus, errorThrown) {

			}
		});
	}

	$(document).ajaxComplete(function() {
		initialise();
	});
</script>