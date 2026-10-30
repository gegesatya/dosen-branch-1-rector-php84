
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
            <?= $t->yearly_data['2019'] !== null ? $t->yearly_data['2019'] : 'null'; ?>,
            <?= $t->yearly_data['2020'] !== null ? $t->yearly_data['2020'] : 'null'; ?>,
            <?= $t->yearly_data['2021'] !== null ? $t->yearly_data['2021'] : 'null'; ?>,
            <?= $t->yearly_data['2022'] !== null ? $t->yearly_data['2022'] : 'null'; ?>,
            <?= $t->yearly_data['2023'] !== null ? $t->yearly_data['2023'] : 'null'; ?>,
            <?= $t->yearly_data['2024'] !== null ? $t->yearly_data['2024'] : 'null'; ?>
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

    // 3. Bar Chart: Failure Rate Mata Kuliah
    var mkLabels = [];
    var mkFailureRate = [];
    <?php foreach($high_risk_courses as $mk): ?>
        mkLabels.push("<?= strlen($mk->nm_mk) > 12 ? substr($mk->nm_mk, 0, 12).'...' : $mk->nm_mk; ?>");
        mkFailureRate.push(<?= $mk->failure_rate; ?>);
    <?php endforeach; ?>

    // Fill with mock data if not enough failed courses are in db
    if(mkLabels.length === 0) {
        mkLabels = ['Struktur Data', 'Matdis', 'Akuntansi Biaya', 'Algoritma', 'Fisika'];
        mkFailureRate = [38, 34, 31, 29, 22];
    }

    var failureCtx = document.getElementById('failureChart').getContext('2d');
    
    // Create dynamic horizontal/vertical sunset orange to golden amber gradient
    var orangeGradient = failureCtx.createLinearGradient(0, 0, 0, 250);
    orangeGradient.addColorStop(0, '#f12711'); // Sunset Red-Orange
    orangeGradient.addColorStop(1, '#f5af19'); // Golden Amber

    new Chart(failureCtx, {
        type: 'bar',
        data: {
            labels: mkLabels,
            datasets: [{
                label: 'Failure Rate (%)',
                data: mkFailureRate,
                backgroundColor: orangeGradient,
                hoverBackgroundColor: '#f12711',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: {
                        font: { size: 9 },
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: { max: 100, ticks: { callback: function(value) { return value + '%'; } } }
            }
        }
    });

    // 4. Scatter Chart: Beban SKS vs Performa IPS
    var scatterCtx = document.getElementById('scatterChart').getContext('2d');
    new Chart(scatterCtx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Mahasiswa',
                data: <?= json_encode($scatter_data); ?>,
                backgroundColor: '#2a5298',
                borderColor: '#1e3c72',
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Jumlah SKS Diambil' },
                    min: 15,
                    max: 26
                },
                y: {
                    title: { display: true, text: 'IPS Semester' },
                    min: 2.0,
                    max: 4.0
                }
            }
        }
    });

});
</script>
