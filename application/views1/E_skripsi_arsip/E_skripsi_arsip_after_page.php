
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    var table;

    var TableAjax = function() {
        var handleRecords = function() {
            table = $('#datatable_ajax').DataTable({
                "processing": true,
                "serverSide": true,
                "pagingType": "full_numbers",
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('E_skripsi_arsip/ajax_list') ?>",
                    "type": "POST",
                    data: function(dtRequest) {
                        dtRequest['<?php echo $this->security->get_csrf_token_name(); ?>'] =
                            '<?php echo $this->security->get_csrf_hash(); ?>';
                        return dtRequest;
                    },
                },

                "columnDefs": [
                    {
                        "targets": "_all",
                        "orderable": false,
                    },
                    {
                        "targets": 3,
                        "className": "col-judul"
                    }
                ]

            });
        }

        return {
            init: function() {
                handleRecords();
            }

        };

    }();

    function reload_table() {
        table.ajax.reload(null, false);
    }

    // ----------------------------------------------------
    // INITIALIZE CHARTS
    // ----------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Donut Chart: Distribusi Nilai TA
        var donutCtx = document.getElementById('gradeDonutChart').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($donut_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($donut_data); ?>,
                    backgroundColor: ['#22c55e', '#3b82f6', '#06b6d4', '#eab308', '#f97316', '#a855f7'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, usePointStyle: true, font: { size: 10 } }
                    }
                },
                cutout: '60%'
            }
        });

        // 2. Pie Chart: Ketepatan Waktu Kelulusan
        var pieCtx = document.getElementById('timePieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($pie_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($pie_data); ?>,
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, usePointStyle: true, font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
