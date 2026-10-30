<script>
$(document).ready(function() {
    var limit = 10;
    var offset = 0;
    var loading = false;
    var cardIndex = 1; // Global sequential card numbering
    var activeAcademicYear = "<?= isset($stats->tahun_ajaran_aktif) ? $stats->tahun_ajaran_aktif : ''; ?>";
    
    // Filter State
    var activeProdiFilter = "WBI";
    var activeStatusFilters = [];

    // Filter Listeners
    $('.filter-group-prodi .filter-pill').on('click', function() {
        $('.filter-group-prodi .filter-pill').removeClass('active');
        $(this).addClass('active');
        activeProdiFilter = $(this).data('value');
    });

    $('.filter-group-status .filter-pill').on('click', function() {
        if ($(this).hasClass('active')) {
            // Jika diklik lagi pada status yang sudah aktif, matikan filternya
            $(this).removeClass('active');
            activeStatusFilters = [];
        } else {
            // Jika memilih status baru, matikan yang lain dan aktifkan yang dipilih
            $('.filter-group-status .filter-pill').removeClass('active');
            $(this).addClass('active');
            activeStatusFilters = [$(this).data('value')];
        }
    });

    $('#btn-apply-filter').on('click', function() {
        resetAndLoadFeed();
    });

    $('#btn-reset-filter').on('click', function() {
        // Reset state
        activeProdiFilter = "WBI";
        activeStatusFilters = [];
        
        // Reset UI
        $('.filter-pill').removeClass('active');
        $('.filter-group-prodi .filter-pill[data-value="WBI"]').addClass('active');
        
        resetAndLoadFeed();
    });

    function resetAndLoadFeed() {
        offset = 0;
        cardIndex = 1;
        $('#timeline-feed').empty();
        $('#timeline-feed').html('<div id="feed-loader" style="text-align:center; padding: 80px 20px;"><div class="modern-spinner"></div><p class="loader-text" style="margin-top: 25px;">MEMUAT LINIMASA TA...</p></div>');
        $('#btn-load-more').prop('disabled', false).show();
        if (!loading) {
            loadTimelineFeed();
        }
    }

    // Load first page of data
    loadTimelineFeed();

    // Attach click handler to Load More button
    $('#btn-load-more').on('click', function() {
        if (!loading) {
            loadTimelineFeed();
        }
    });

    function getInitials(name) {
        if (!name) return "TA";
        var parts = name.trim().split(/\s+/);
        var initials = parts[0].charAt(0);
        if (parts.length > 1) {
            initials += parts[parts.length - 1].charAt(0);
        } else if (parts[0].length > 1) {
            initials += parts[0].charAt(1);
        }
        return initials.toUpperCase();
    }

    // Helper to calculate exact age in years, months, and days
    function getDurationString(tgl_penetapan) {
        if (!tgl_penetapan) return "-";
        var start = new Date(tgl_penetapan);
        var end = new Date();
        if (isNaN(start.getTime())) return "-";
        
        var years = end.getFullYear() - start.getFullYear();
        var months = end.getMonth() - start.getMonth();
        var days = end.getDate() - start.getDate();
        
        if (days < 0) {
            var prevMonth = new Date(end.getFullYear(), end.getMonth(), 0);
            days += prevMonth.getDate();
            months--;
        }
        if (months < 0) {
            months += 12;
            years--;
        }
        
        var parts = [];
        if (years > 0) parts.push(years + " tahun");
        if (months > 0) parts.push(months + " bulan");
        if (days > 0) parts.push(days + " hari");
        
        if (parts.length === 0) return "0 hari";
        return parts.join(" ");
    }

    function loadTimelineFeed() {
        loading = true;
        $('#load-spinner').show();
        $('#btn-load-more').prop('disabled', true);
        $('#btn-text').text('Memuat data...');

        $.ajax({
            url: "<?= site_url('C_linimasa_ta/get_timeline_feed') ?>",
            type: "GET",
            data: {
                limit: limit,
                offset: offset,
                prodi: activeProdiFilter,
                statuses: activeStatusFilters
            },
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var data = response.data;
                    var totalCount = response.total_count || 0;
                    
                    // Update the result info
                    if (totalCount > 0) {
                        $('#filter-total-count').text(totalCount);
                        $('#filter-result-info').show();
                    } else {
                        $('#filter-result-info').hide();
                    }

                    if (data.length > 0) {
                        setTimeout(function() {
                            $('#feed-loader').remove();
                            appendCards(data, totalCount);
                            offset += data.length;

                            // Restore button
                            $('#load-spinner').hide();
                            $('#btn-load-more').prop('disabled', false);
                            $('#btn-text').text('Tampilkan Lebih Banyak');

                            if (data.length < limit) {
                                // No more items to load
                                $('#btn-load-more').prop('disabled', true).hide();
                            }
                        }, 1000);
                    } else {
                        // Empty response
                        $('#load-spinner').hide();
                        if (offset === 0) {
                            setTimeout(function() {
                                $('#feed-loader').remove();
                                $('#timeline-feed').html(
                                    '<div class="empty-state">' +
                                    '  <div class="empty-state-icon"><i class="fa fa-folder-open-o"></i></div>' +
                                    '  <p class="empty-state-text">Tidak ada data mahasiswa Tugas Akhir aktif ditemukan.</p>' +
                                    '</div>'
                                );
                            }, 1000);
                        }
                        $('#btn-load-more').prop('disabled', true).hide();
                    }
                } else {
                    showErrorState();
                }
            },
            error: function() {
                showErrorState();
            },
            complete: function() {
                loading = false;
            }
        });
    }

    function showErrorState() {
        $('#load-spinner').hide();
        $('#feed-loader').remove();
        $('#btn-load-more').prop('disabled', false);
        $('#btn-text').text('Coba Lagi');
        alert('Gagal memuat data linimasa. Silakan coba beberapa saat lagi.');
    }

    function appendCards(students, totalCount) {
        var feed = $('#timeline-feed');
        
        students.forEach(function(stu) {
            var isSempro = parseInt(stu.sempro_layak) > 0;
            var isSemhas = parseInt(stu.semhas_layak) > 0;
            var isSidang = parseInt(stu.sidang_lulus) > 0;

            // Determine card class based on progress
            var cardProgressClass = '';
            var progressLineWidth = '0%';
            var step1Class = 'completed';
            var step2Class = '';
            var step3Class = '';
            var step4Class = '';

            if (isSidang) {
                cardProgressClass = 'progress-sidang';
                progressLineWidth = '100%';
                step2Class = 'completed';
                step3Class = 'completed';
                step4Class = 'completed';
            } else if (isSemhas) {
                cardProgressClass = 'progress-semhas';
                progressLineWidth = '66.6%';
                step2Class = 'completed';
                step3Class = 'completed';
                step4Class = 'active-step';
            } else if (isSempro) {
                cardProgressClass = 'progress-sempro';
                progressLineWidth = '33.3%';
                step2Class = 'completed';
                step3Class = 'active-step';
            } else {
                cardProgressClass = '';
                progressLineWidth = '0%';
                step2Class = 'active-step';
            }

            // Determine age badge warning level and label
            var ageBadgeClass = 'time-badge';
            var ageLabel = '';
            
            if (stu.sidang_lulus > 0) {
                if (stu.is_lulus_tepat_waktu) {
                    ageBadgeClass += ' alert-appreciation';
                    ageLabel = '<i class=\"fa fa-star\" style=\"color: #F6E05E; margin-right: 4px;\"></i> Lulus Tepat Waktu (' + stu.umur_hari_text + ')';
                } else {
                    ageBadgeClass += ' alert-old';
                    ageLabel = '<i class=\"fa fa-exclamation-triangle\" style=\"margin-right: 4px;\"></i> Selesai lebih dari 6 bulan (' + stu.umur_hari_text + ')';
                }
            } else {
                ageLabel = '<i class="fa fa-calendar-check-o"></i> Umur: ' + stu.umur_hari_text;
                if (stu.umur_hari > 180) {
                    ageBadgeClass += ' alert-old';
                    ageLabel += ' (Terlambat > 6 bln)';
                } else if (stu.umur_hari > 90) {
                    ageBadgeClass += ' alert-medium';
                    ageLabel += ' (Waspada > 3 bln)';
                } else {
                    ageLabel += ' (Aman < 3 bln)';
                }
            }

            // Build advisers HTML
            var advisorsHtml = '';
            if (stu.pembimbing_1_name) {
                advisorsHtml += '<div class="advisor-item">' +
                                '  <span class="advisor-badge-primary">Utama</span>' +
                                '  <span>' + stu.pembimbing_1_name + '</span>' +
                                '</div>';
            }
            if (stu.pembimbing_2_name) {
                advisorsHtml += '<div class="advisor-item">' +
                                '  <span class="advisor-badge-sec">Pendamping</span>' +
                                '  <span>' + stu.pembimbing_2_name + '</span>' +
                                '</div>';
            }

            // Student avatar img or initials fallback
            var avatarHtml = '';
            if (stu.avatar_url && stu.avatar_url.trim() !== '') {
                avatarHtml = '<img class="avatar-circle" src="' + stu.avatar_url + '" alt="' + stu.nm_pd + '" ' +
                             'onerror="this.onerror=null; $(this).replaceWith(\'<div class=\\\'avatar-circle avatar-initials\\\'>\' + getInitials(this.alt) + \'</div>\');">';
            } else {
                avatarHtml = '<div class="avatar-circle avatar-initials">' + getInitials(stu.nm_pd) + '</div>';
            }

            var nilaiMutuHtml = '';
            if (stu.sidang_lulus > 0 && stu.sidang_nilai_mutu) {
                nilaiMutuHtml = '<div style="margin-top: 5px;"><span style="background-color: #3182CE; color: white; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">Nilai: ' + stu.sidang_nilai_mutu + '</span></div>';
            }

            var cardHtml = 
                '<div class="student-card ' + cardProgressClass + '" style="opacity: 0; transform: translateY(15px); transition: all 0.4s ease;">' +
                '  <div class="card-number-badge">#' + cardIndex + ' / ' + totalCount + '</div>' + // Sequential rank number badge
                '  <div class="card-header-flex">' +
                     avatarHtml +
                '    <div class="student-meta">' +
                '      <h3 class="student-name" style="padding-right: 45px;">' + stu.nm_pd + '</h3>' +
                '      <p class="student-nim-prodi" style="margin-bottom: 3px;">' +
                '        <strong>NIM: ' + stu.nipd + '</strong>' +
                '        <span class="prodi-badge">' + stu.prodi + '</span>' +
                '      </p>' +
                '      <div style="font-size: 11px; color: #718096;"><i class="fa fa-user" style="margin-right: 4px;"></i>Dosen Wali: <strong>' + (stu.dosen_wali_name ? stu.dosen_wali_name : 'Belum Ditentukan') + '</strong></div>' +
                '    </div>' +
                '  </div>' +
                '  <div class="ta-title-box">' +
                '    <div class="ta-title-label">Judul Tugas Akhir</div>' +
                '    <p class="ta-title-text">' + stu.judul_ditetapkan + '</p>' +
                '  </div>' +
                '  <div class="advisors-section">' +
                     advisorsHtml +
                '  </div>' +
                '  <div class="krs-section" style="margin-top: 10px; margin-bottom: 15px; font-size: 11px; color: #4A5568; background-color: #EDF2F7; padding: 6px 10px; border-radius: 4px; display: flex; align-items: center; flex-wrap: wrap; gap: 5px;">' +
                '    <i class="fa fa-file-text-o" style="font-size: 13px;"></i> KRS MK Tugas Akhir: ' + 
                     (stu.krs_smt ? ('<strong>Smt ' + stu.krs_smt + ' (' + stu.krs_date_fmt + ')</strong>' + (stu.krs_smt != activeAcademicYear ? ' <span style="background: #FFF5F5; color: #E53E3E; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; border: 1px solid #FEB2B2; margin-left: 5px;"><i class="fa fa-exclamation-triangle"></i> KRS Lama / Belum Aktif</span>' : '')) : '<span style="color: #E53E3E; font-weight: bold;">Belum/Tidak Ditemukan</span>') +
                '  </div>' +
                '  <div class="milestone-stepper">' +
                '    <div class="stepper-progress-line" style="width: ' + progressLineWidth + '"></div>' +
                '    <div class="milestone-step ' + step1Class + '">' +
                '      <div class="step-node"><i class="fa fa-check"></i><span>1</span></div>' +
                '      <div class="step-label">Judul</div>' +
                '      <div class="step-date" style="font-size: 10px; color: #718096; margin-top: 2px;">' + stu.tanggal_formatted + '</div>' +
                '    </div>' +
                '    <div class="milestone-step ' + step2Class + '">' +
                '      <div class="step-node"><i class="fa fa-check"></i><span>2</span></div>' +
                '      <div class="step-label">Sempro</div>' +
                '      <div class="step-date" style="font-size: 10px; color: #718096; margin-top: 2px;">' + (stu.sempro_tanggal_fmt || '&nbsp;') + '</div>' +
                '    </div>' +
                '    <div class="milestone-step ' + step3Class + '">' +
                '      <div class="step-node"><i class="fa fa-check"></i><span>3</span></div>' +
                '      <div class="step-label">Semhas</div>' +
                '      <div class="step-date" style="font-size: 10px; color: #718096; margin-top: 2px;">' + (stu.semhas_tanggal_fmt || '&nbsp;') + '</div>' +
                '    </div>' +
                '    <div class="milestone-step ' + step4Class + '">' +
                '      <div class="step-node"><i class="fa fa-check"></i><span>4</span></div>' +
                '      <div class="step-label">Sidang</div>' +
                '      <div class="step-date" style="font-size: 10px; color: #718096; margin-top: 2px;">' + (stu.sidang_tanggal_fmt || '&nbsp;') + '</div>' +
                       nilaiMutuHtml +
                '    </div>' +
                '  </div>' +
                '  <div class="card-footer-info">' +
                '    <div>Ditetapkan: ' + stu.tanggal_formatted + '</div>' +
                '    <div class="' + ageBadgeClass + '">' +
                '      ' + ageLabel +
                '    </div>' +
                '  </div>' +
                '</div>';

            cardIndex++; // Increment badge counter for the next card

            var cardElement = $(cardHtml);
            feed.append(cardElement);

            // Trigger animation fade-in after append
            setTimeout(function() {
                cardElement.css({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                });
            }, 50);
        });
    }

    // Initialize Chart.js for Prodi Performance Chart
    var ctx = document.getElementById('prodiPerformanceChart').getContext('2d');
    var prodiData = <?= json_encode($stats->prodi_details); ?>;
    
    var labels = [];
    var tinggiData = [];
    var sedangData = [];
    var rendahData = [];
    
    for (var prodi in prodiData) {
        if (prodiData.hasOwnProperty(prodi)) {
            // Shorten extremely long prodi names for the chart labels
            var displayProdi = prodi;
            if (prodi.length > 25) {
                displayProdi = prodi.substring(0, 22) + '...';
            }
            labels.push(displayProdi);
            tinggiData.push(prodiData[prodi].tinggi);
            sedangData.push(prodiData[prodi].sedang);
            rendahData.push(prodiData[prodi].rendah);
        }
    }
    
    var performanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Tinggi (Semhas/Sidang)',
                    data: tinggiData,
                    backgroundColor: '#48BB78',
                    borderWidth: 0,
                    borderRadius: 4
                },
                {
                    label: 'Sedang (Sempro)',
                    data: sedangData,
                    backgroundColor: '#D69E2E',
                    borderWidth: 0,
                    borderRadius: 4
                },
                {
                    label: 'Rendah (Judul Baru)',
                    data: rendahData,
                    backgroundColor: '#CBD5E0',
                    borderWidth: 0,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Horizontal bar chart
            scales: {
                x: {
                    stacked: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#718096',
                        font: {
                            family: 'Inter',
                            size: 10
                        }
                    }
                },
                y: {
                    stacked: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#2D3748',
                        font: {
                            family: 'Inter',
                            size: 10,
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        usePointStyle: true,
                        font: {
                            family: 'Inter',
                            size: 10
                        }
                    }
                }
            }
        }
    });
});

// Global context for modal function
var gProdiData = <?= json_encode($stats->prodi_details); ?>;
var prodiRiskChartInstance = null;
var krsMissingChartInstance = null;
var dosenKritisChartInstance = null;
var progressTahapanChartInstance = null;
var kelulusanTepatWaktuChartInstance = null;

function getLecturerInitials(name) {
    if (!name || name === 'Belum Ditentukan') return 'NA';
    
    // 1. Hapus semua gelar di belakang koma (misal: , S.Kom., M.T.)
    var cleanName = name.split(',')[0];
    
    // 2. Hapus gelar awalan umum (dengan atau tanpa titik)
    cleanName = cleanName.replace(/\b(Dr|Drs|Dra|Ir|Prof|H|Hj|KH|Ns)\b\.?/gi, '');
    
    // 3. Hapus singkatan huruf lain yang diakhiri titik (misal: A., M.)
    cleanName = cleanName.replace(/\b[A-Za-z]+\./g, '');
    
    // Bersihkan spasi berlebih
    cleanName = cleanName.trim();
    if (!cleanName) cleanName = name.split(',')[0].trim(); // Fallback
    
    var parts = cleanName.split(/\s+/);
    var initials = parts[0].charAt(0);
    if (parts.length > 1) {
        initials += parts[parts.length - 1].charAt(0);
    } else if (parts[0].length > 1) {
        initials += parts[0].charAt(1);
    }
    return initials.toUpperCase();
}

function showProdiDetail(prodiName) {
    var pData = gProdiData[prodiName];
    if (!pData) return;

    $('#mdlProdiName').text(prodiName);
    $('#modal-executive-summary').html(pData.narasi);
    $('#modal-strong-point').html(pData.strong_point);
    $('#modal-weak-point').html(pData.weak_point);

    // 1. Distribusi Waktu Pengerjaan Chart
    var ctxRisk = document.getElementById('prodiRiskChart').getContext('2d');
    if (prodiRiskChartInstance) { prodiRiskChartInstance.destroy(); }
    prodiRiskChartInstance = new Chart(ctxRisk, {
        type: 'doughnut',
        data: {
            labels: ['Baru (< 6 Bln)', 'Berjalan (6-12 Bln)', 'Kritis (> 1 Thn)'],
            datasets: [{
                data: [pData.waktu_baru, pData.waktu_menengah, pData.waktu_kritis],
                backgroundColor: ['#4FD1C5', '#F6AD55', '#FC8181'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 10 }, usePointStyle: true, boxWidth: 8 } }
            }
        }
    });

    // 2. KRS Status Chart
    var ctxKrs = document.getElementById('krsMissingChart').getContext('2d');
    if (krsMissingChartInstance) { krsMissingChartInstance.destroy(); }
    krsMissingChartInstance = new Chart(ctxKrs, {
        type: 'pie',
        data: {
            labels: ['Aktif KRS', 'Menghilang'],
            datasets: [{
                data: [pData.krs_aktif, pData.krs_menghilang],
                backgroundColor: ['#38A169', '#E53E3E'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 10 }, usePointStyle: true, boxWidth: 8 } }
            }
        }
    });

    // 3. Dosen Kritis Chart (Top 5)
    var ctxDosen = document.getElementById('dosenKritisChart').getContext('2d');
    if (dosenKritisChartInstance) { dosenKritisChartInstance.destroy(); }
    
    var dosenLabels = [];
    var dosenData = [];
    var count = 0;
    if (pData.dosen_kritis) {
        for (var dosenName in pData.dosen_kritis) {
            if (count >= 5) break; // Only show top 5 worst
            dosenLabels.push(getLecturerInitials(dosenName));
            dosenData.push(pData.dosen_kritis[dosenName]);
            count++;
        }
    }

    dosenKritisChartInstance = new Chart(ctxDosen, {
        type: 'bar',
        data: {
            labels: dosenLabels,
            datasets: [{
                label: 'Mhs Kritis',
                data: dosenData,
                backgroundColor: '#3182CE',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            var initials = context[0].label;
                            for (var d in pData.dosen_kritis) {
                                if (getLecturerInitials(d) === initials) return d;
                            }
                            return initials;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                x: { ticks: { font: { size: 10 } } }
            }
        }
    });

    // 4. Progress Tahapan Chart (Mutually Exclusive)
    var ctxProgress = document.getElementById('progressTahapanChart').getContext('2d');
    if (progressTahapanChartInstance) { progressTahapanChartInstance.destroy(); }
    progressTahapanChartInstance = new Chart(ctxProgress, {
        type: 'bar',
        data: {
            labels: ['Sidang', 'Semhas', 'Sempro', 'Belum'],
            datasets: [{
                label: 'Jumlah Mhs',
                data: [pData.tahap_sidang, pData.tahap_semhas, pData.tahap_sempro, pData.tahap_belum],
                backgroundColor: ['#2F855A', '#3182CE', '#D69E2E', '#A0AEC0'],
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
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                x: { ticks: { font: { size: 10 } } }
            }
        }
    });

    // 5. Kelulusan Tepat Waktu Chart
    var ctxLulus = document.getElementById('kelulusanTepatWaktuChart').getContext('2d');
    if (kelulusanTepatWaktuChartInstance) { kelulusanTepatWaktuChartInstance.destroy(); }
    kelulusanTepatWaktuChartInstance = new Chart(ctxLulus, {
        type: 'doughnut',
        data: {
            labels: ['Tepat Waktu (≤ 6 Bln)', 'Terlambat (> 6 Bln)'],
            datasets: [{
                data: [pData.lulus_tepat_waktu, pData.lulus_terlambat],
                backgroundColor: ['#38A169', '#E53E3E'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 10 }, usePointStyle: true, boxWidth: 8 } }
            }
        }
    });

    $('#modalProdiDetail').modal('show');
}
</script>
