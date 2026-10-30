<!--[if lt IE 9]>
<script src="<?= base_url('assets/theme/assets/global/plugins/respond.min.js'); ?>"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/excanvas.min.js'); ?>"></script> 
<![endif]-->
<script src="<?= base_url('assets/theme/assets/global/plugins/jquery.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/jquery-migrate.min.js'); ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/morris/raphael-min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/morris/morris.min.js'); ?>"></script>

<script src="<?= base_url('assets/theme/assets/global/plugins/jquery-ui/jquery-ui.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/bootstrap/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/jquery.blockui.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/global/plugins/jquery.cokie.min.js'); ?>" type="text/javascript"></script>
<!-- <script src="<?= base_url('assets/theme/assets/global/plugins/uniform/jquery.uniform.min.js'); ?>" type="text/javascript"></script> -->
<script src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js'); ?>" type="text/javascript"></script>

<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/select2/select2.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js'); ?>"></script>

<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/moment.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/jquery.mockjax.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/theme/assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js'); ?>"></script>

<script src="<?= base_url('assets/theme/assets/global/scripts/metronic.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/admin/layout4/scripts/layout.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/admin/layout4/scripts/demo.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('assets/theme/assets/global/scripts/datatable.js'); ?>"></script>

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<?php
if (isset($after_page)) {
  $this->load->view($after_page);
}
?>

<script>
  jQuery(document).ready(function() {
    Metronic.init();
    Layout.init();
    Demo.init();
    if (typeof TableAjax !== 'undefined') {
      TableAjax.init();
    }
  });
</script>
