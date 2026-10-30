import re

file_path = r'c:\laragon\www\wbi\server\dosen\application\views\_layout\_main_menu.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = re.sub(r'<i class="[^"]+"></i>(\s*<span class="title">Perwalian)', r'<i class="icon-users"></i>\1', content)
content = re.sub(r'<i class="[^"]+"></i>(\s*<span class="title">LAPKERMA)', r'<i class="icon-link"></i>\1', content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
