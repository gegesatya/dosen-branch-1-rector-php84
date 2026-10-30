import re

file_path = r'c:\laragon\www\wbi\server\dosen\application\views\_layout\_main_menu.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Map the FA6 icons we added to FA4 / Simple Line Icons
replacements = {
    'fa-solid fa-chart-pie': 'icon-pie-chart',
    'fa-solid fa-users': 'icon-users',
    'fa-solid fa-graduation-cap': 'icon-graduation',
    'fa-solid fa-brain': 'icon-bulb',
    'fa-solid fa-timeline': 'icon-action-redo',
    
    'fa-solid fa-chalkboard-user': 'icon-screen-desktop',
    'fa-solid fa-calendar-days': 'icon-calendar',
    'fa-solid fa-clipboard-list': 'icon-list',
    'fa-solid fa-upload': 'icon-cloud-upload',
    'fa-solid fa-clipboard-user': 'icon-user-following',
    'fa-solid fa-gauge': 'icon-speedometer',
    'fa-solid fa-file-signature': 'icon-pencil',
    'fa-solid fa-user-check': 'icon-user-follow',
    'fa-solid fa-star': 'icon-star',
    'fa-solid fa-check-double': 'icon-check',
    
    'fa-solid fa-clock-rotate-left': 'icon-clock',
    'fa-solid fa-person-chalkboard': 'icon-notebook',
    'fa-solid fa-book-open': 'icon-book-open',
    
    'fa-solid fa-users-viewfinder': 'icon-people',
    'fa-solid fa-file-circle-check': 'icon-check',
    'fa-solid fa-pen-to-square': 'icon-note',
    'fa-solid fa-file-circle-xmark': 'icon-close',
    'fa-solid fa-chart-line': 'icon-graph',
    'fa-solid fa-comments': 'icon-bubbles',
    
    'fa-solid fa-file-lines': 'icon-docs',
    'fa-solid fa-plus': 'icon-plus',
    'fa-solid fa-folder-open': 'icon-folder',
    'fa-solid fa-sitemap': 'icon-organization',
    'fa-solid fa-list': 'icon-list',
    'fa-solid fa-chalkboard': 'icon-screen-tablet',
    'fa-solid fa-book': 'icon-book-open',
    
    'fa-solid fa-bell': 'icon-bell',
    'fa-solid fa-pen-nib': 'icon-pencil',
    'fa-solid fa-calendar-check': 'icon-calendar',
    'fa-solid fa-calendar': 'icon-calendar',
    'fa-solid fa-gavel': 'icon-legal',
    'fa-solid fa-file-invoice': 'icon-doc',
    
    'fa-solid fa-user-tie': 'icon-user',
    'fa-solid fa-people-arrows': 'icon-directions',
    'fa-solid fa-tv': 'icon-screen-desktop',
    'fa-solid fa-scale-balanced': 'icon-equalizer',
    'fa-solid fa-file-shield': 'icon-shield',
    
    'fa-solid fa-briefcase': 'icon-briefcase',
    'fa-solid fa-database': 'icon-layers',
    'fa-solid fa-file-pdf': 'fa fa-file-pdf-o',
    
    'fa-solid fa-folder-tree': 'icon-folder',
    'fa-solid fa-user': 'icon-user',
    
    'fa-solid fa-building-user': 'icon-home',
    'fa-solid fa-users-gear': 'icon-users',
    
    'fa-solid fa-crown': 'icon-badge',
    
    'fa-solid fa-handshake': 'fa fa-handshake-o',
    'fa-solid fa-list-check': 'icon-list',
    
    'fa-solid fa-magnifying-glass-chart': 'icon-magnifier',
    
    'fa-solid fa-key': 'icon-key',
    'fa-solid fa-lock': 'icon-lock',
    'fa-solid fa-right-from-bracket': 'icon-logout',
    'fa-solid fa-gear': 'icon-settings',
    
    'fa-solid fa-folder': 'icon-folder',
    'fa-solid fa-minus': 'icon-arrow-right',
    'icon-diamond': 'icon-folder',
    'icon-badge': 'icon-arrow-right'
}

for old, new_icon in replacements.items():
    content = content.replace(f'class="{old}"', f'class="{new_icon}"')

# Write back
with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Icons replaced with Simple Line Icons / FA4 successfully.")
