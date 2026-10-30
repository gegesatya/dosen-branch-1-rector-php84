import re

file_path = r'c:\laragon\www\wbi\server\dosen\application\views\_layout\_main_menu.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Map specific text patterns to specific icons
# Note: Since the icons are placed before the text, we will use regex to find `<i class="icon-diamond"></i>` or `<i class="icon-badge"></i>` followed by whitespace and the text

replacements = {
    r'Infografis WBI': 'fa-solid fa-chart-pie',
    r'Profil Mahasiswa 2025': 'fa-solid fa-users',
    r'Profil Lulusan WBI': 'fa-solid fa-graduation-cap',
    r'Academic Intelligence': 'fa-solid fa-brain',
    r'Linimasa TA': 'fa-solid fa-timeline',
    
    r'Perkuliahan TA': 'fa-solid fa-chalkboard-user',
    r'Jadwal Dosen': 'fa-solid fa-calendar-days',
    r'Skema Nilai': 'fa-solid fa-clipboard-list',
    r'Publish Nilai': 'fa-solid fa-upload',
    r'Submit Nilai': 'fa-solid fa-upload',
    r'Evaluasi Diri': 'fa-solid fa-clipboard-user',
    r'Dashboard': 'fa-solid fa-gauge',
    r'Berita Acara Perkuliahan': 'fa-solid fa-file-signature',
    r'Kehadiran': 'fa-solid fa-user-check',
    r'Nilai MBKM/KKN/PKL': 'fa-solid fa-star',
    r'Nilai': 'fa-solid fa-check-double',
    
    r'History': 'fa-solid fa-clock-rotate-left',
    r'History Ajar': 'fa-solid fa-person-chalkboard',
    r'History TA': 'fa-solid fa-book-open',
    
    r'Perwalian': 'fa-solid fa-users-viewfinder',
    r'Approval KRS': 'fa-solid fa-file-circle-check',
    r'Sedang Mendaftar KRS': 'fa-solid fa-pen-to-square',
    r'Belum KRS': 'fa-solid fa-file-circle-xmark',
    r'Keberhasilan Studi': 'fa-solid fa-chart-line',
    r'Konsultasi Nilai': 'fa-solid fa-comments',
    
    r'RPS \(NEW\)': 'fa-solid fa-file-lines',
    r'Tambah RPS': 'fa-solid fa-plus',
    r'Daftar RPS Lama': 'fa-solid fa-folder-open',
    r'MK PRODI': 'fa-solid fa-sitemap',
    r'List RPS WBI': 'fa-solid fa-list',
    r'Kelas Perkuliahan': 'fa-solid fa-chalkboard',
    r'RPS': 'fa-solid fa-book',
    
    r'E-TUGAS AKHIR': 'fa-solid fa-graduation-cap',
    r'Notifikasi': 'fa-solid fa-bell',
    r'Bimbingan TA': 'fa-solid fa-users',
    r'Pendaftaran Seminar & Sidang': 'fa-solid fa-pen-nib',
    r'Jadwal Sidang / Seminar': 'fa-solid fa-calendar-check',
    r'Jadwal Seminar / Sidang': 'fa-solid fa-calendar',
    r'Penguji Sidang / Seminar': 'fa-solid fa-gavel',
    r'Riwayat Mahasiswa Bimbingan': 'fa-solid fa-clock-rotate-left',
    r'Rekap Bimbingan Dosen': 'fa-solid fa-file-invoice',
    
    r'Admin Tugas Akhir': 'fa-solid fa-user-tie',
    r'Plotting Pembimbing': 'fa-solid fa-people-arrows',
    r'Monitoring TA': 'fa-solid fa-tv',
    r'Beban Bimbingan Dosen': 'fa-solid fa-scale-balanced',
    r'Validasi Berkas Pendaftaran': 'fa-solid fa-file-shield',
    
    r'E-KINERJA': 'fa-solid fa-briefcase',
    r'Data Kinerja': 'fa-solid fa-database',
    r'Laporan Kinerja': 'fa-solid fa-file-pdf',
    
    r'Rekap Semester': 'fa-solid fa-folder-tree',
    r'Dosen': 'fa-solid fa-user',
    
    r'Internship/Magang': 'fa-solid fa-building-user',
    r'Bimbingan Magang': 'fa-solid fa-users-gear',
    
    r'KAPRODI': 'fa-solid fa-crown',
    
    r'LAPKERMA': 'fa-solid fa-handshake',
    r'Daftar Lapkerma': 'fa-solid fa-list-check',
    
    r'Audit Mutu': 'fa-solid fa-magnifying-glass-chart',
    r'AMI': 'fa-solid fa-chart-pie',
    
    r'Setting API Key': 'fa-solid fa-key',
    r'Change Password': 'fa-solid fa-lock',
    r'Logout': 'fa-solid fa-right-from-bracket',
    r'Setting': 'fa-solid fa-gear'
}

# The icons are often formatted like:
# <i class="icon-diamond"></i>
# <span class="title">Text</span>
# OR
# <i class="icon-badge"></i> Text
# We need to replace icon-diamond and icon-badge with the corresponding mapped icon.

for key, val in replacements.items():
    # Regex explanation:
    # Match <i class="icon-diamond"></i> or <i class="icon-badge"></i>
    # Followed by optional whitespace and possibly <span class="title">
    # Followed by the key text
    pattern = r'<i class="(?:icon-diamond|icon-badge|icon-key)"></i>(\s*(?:<span class="title">)?\s*' + key + r')'
    content = re.sub(pattern, f'<i class="{val}"></i>\\1', content)

# Write back
with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Icons replaced successfully.")
