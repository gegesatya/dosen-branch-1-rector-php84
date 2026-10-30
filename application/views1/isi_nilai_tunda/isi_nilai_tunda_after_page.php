<script type="text/javascript">
	$(document).ready(function() {
		$('#kontrol').hide();
		$('#tabel_kehadiran').hide();

		$('select[name=ddKelasKuliah]').on('change', function() {
			dropdown_change($(this).val());
		});

		initialise();
		show_bantuan();
		$('#form_submit').submit(function(e) {
			e.preventDefault();
			$.ajax({
				url: '<?php echo site_url('C_isi_nilai_excel_tunda/save_excel/') ?>',
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
				url: "<?php echo site_url('C_isi_nilai_excel_tunda/save_id_kls_session/') ?>" + id_kls,
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
			url: '<?php echo site_url(); ?>C_isi_nilai_excel_tunda/save',
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
			url: "<?php echo site_url('C_isi_nilai_excel_tunda/sinkronisasi/') ?>",
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

	function show_bantuan() {
		$('#modal_form_bantuan').modal('show');
		$('.modal-title').text("Panduan Pengisian Nilai");

	}
</script>
<div class="modal fade" id="modal_form_bantuan" role="dialog">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title">Person Form</h3>
			</div>
			<div class="modal-body form">
				<div class="well margin-20 ">
					<p>Mohon untuk dibaca dengan teliti :</p>
					<ol>
						<li>Download terlebih dulu file excel daftar nilai mahasiswa</li>
						<li>Jika belum mengisi Skema Nilai, isilah Skema Nilai terlebih dahulu <strong>(Penting)</strong></li>
						<li>Editlah form nilai pada file excel tersebut, ikuti petunjuk yang ada pada form excel nilai</li>
						<li>Sistem tidak otomatis memberikan nilai kosong pada kolom UAS jika kehadiran mahasiswa di bawah persen yang diterapkan.</li>
						<li>Pemberian nilai kosong pada kolom UAS jika kehadiran mahasiswa di bawah persen yang diterapkan, dilakukan oleh Dosen pada form excel.</li>
						<li>Jangan lupa untuk menyimpan file excel tersebut setelah diedit</li>
					</ol>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>