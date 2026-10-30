import re

file_path = r'c:\laragon\www\wbi\server\dosen\application\views\_layout\_main_menu.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

dashboard_menu = """
        <li class="<?= $menu_aktif == 'm_dashboard_perwalian' ? 'active' : ''; ?>">
          <a href="<?= site_url('Lecturer_dashboard_perwalian'); ?>">
            <i class="icon-speedometer"></i>
            Dashboard Perwalian
          </a>
        </li>
"""

# Find the list item for m_persetujuan_krs and insert the dashboard menu right before it
target_string = r'<li class="<?= \$menu_aktif == \'m_persetujuan_krs\' \? \'active\' : \'\'\; ?>">'
content = re.sub(f'({target_string})', f'{dashboard_menu}        \\1', content, count=1)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
