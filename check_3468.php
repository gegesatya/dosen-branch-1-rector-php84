<?php
$val = file_get_contents('c:\\laragon\\www\\wbi\\server\\dosen\\kegiatan_3468.json');
$fixed = preg_replace('/\\\\([^\\\\\"\/bfnrtu])/i', '$1', $val);
file_put_contents('c:\\laragon\\www\\wbi\\server\\dosen\\kegiatan_3468_fixed.json', $fixed);
