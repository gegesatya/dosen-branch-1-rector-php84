
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    
    // 1. Line Chart: Tren IPK Per Prodi (Mulai 2019)
    var colors = [
        { border: '#1e3c72', bg: 'rgba(30, 60, 114, 0.03)' },
        { border: '#11998e', bg: 'rgba(17, 153, 142, 0.03)' },
        { border: '#f5af19', bg: 'rgba(245, 175, 25, 0.03)' },
        { border: '#ef473a', bg: 'rgba(239, 71, 58, 0.03)' },
        { border: '#9b59b6', bg: 'rgba(155, 89, 182, 0.03)' }
    ];

    var datasets = [];
    <?php foreach($trend_per_prodi as $index => $t): ?>
        var yearlyData = [
            <?= $t->yearly_data['2019'] ?? 'null'; ?>,
            <?= $t->yearly_data['2020'] ?? 'null'; ?>,
            <?= $t->yearly_data['2021'] ?? 'null'; ?>,
            <?= $t->yearly_data['2022'] ?? 'null'; ?>,
            <?= $t->yearly_data['2023'] ?? 'null'; ?>,
            <?= $t->yearly_data['2024'] ?? 'null'; ?>
        ];
        var color = colors[<?= $index; ?> % colors.length];
        
        datasets.push({
            label: "<?= $t->prodi; ?>",
            data: yearlyData,
            borderColor: color.border,
            backgroundColor: color.bg,
            tension: 0.4,
            fill: true,
            spanGaps: true
        });
    <?php endforeach; ?>

    var ipkCtx = document.getElementById('ipkTrendChart').getContext('2d');
    new Chart(ipkCtx, {
        type: 'line',
        data: {
            labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 10, usePointStyle: true, font: { size: 9 } }
                }
            },
            scales: {
                y: { 
                    min: 2.5, 
                    max: 4.0,
                    ticks: {
                        stepSize: 0.2
                    }
                }
            }
        }
    });

    // 2. Doughnut Chart: Distribusi Academic Health
    var healthCtx = document.getElementById('healthChart').getContext('2d');
    new Chart(healthCtx, {
        type: 'doughnut',
        data: {
            labels: ['Sehat (IPK > 3.0)', 'Waspada (2.5 - 3.0)', 'Kritis (IPK < 2.5)'],
            datasets: [{
                data: <?= json_encode($health_distribution); ?>,
                backgroundColor: ['#11998e', '#f5af19', '#ef473a'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, usePointStyle: true }
                }
            },
            cutout: '70%'
        }
    });



});
</script>
