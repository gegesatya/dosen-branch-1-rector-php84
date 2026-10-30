<style type="text/css">
  /* Common PDF Utility & Reset Styles (Ringan & Mandiri - Pengganti Bootstrap 3) */
  * {
    box-sizing: border-box;
  }

  /* Table styling seragam & rapi seperti HTML (konsisten, bebas tebal-tipis) */
  .table {
    width: 100%;
    max-width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
  }

  .table th,
  .table td {
    padding: 4px 6px;
    vertical-align: middle;
  }

  .table-black,
  table.table-black {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
  }

  .table-black th,
  .table-black td,
  table.table-black th,
  table.table-black td {
    border: 0.5pt solid #334155 !important;
    padding: 4px 6px !important;
    font-size: 11px !important;
  }

  .table-black th,
  table.table-black th {
    font-weight: bold;
    background-color: #f1f5f9;
    color: #0f172a;
    text-align: center;
  }

  .table-black-2 th,
  .table-black-2 td,
  table.table-black-2 th,
  table.table-black-2 td {
    border-top: 0.5pt solid #334155 !important;
    border-bottom: 0.5pt solid #334155 !important;
    border-left: none !important;
    border-right: none !important;
    padding: 4px 6px !important;
    font-size: 11px !important;
  }

  .table-borderless th,
  .table-borderless td {
    border: none !important;
    padding: 3px !important;
    margin-bottom: 10px;
    vertical-align: top;
  }

  .table-borderless2 th,
  .table-borderless2 td {
    border: none !important;
    padding: 4px !important;
    font-size: 11px !important;
    vertical-align: top;
  }

  /* Lecturer Revision Cards (Modern Academic Card Theme) */
  .dosen-card {
    border: 0.5pt solid #94a3b8;
    margin-bottom: 12px;
    background-color: #ffffff;
    page-break-inside: avoid;
  }

  .dosen-card-header {
    background-color: #006a6f;
    color: #ffffff;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: bold;
  }

  .dosen-card-header .nidn-tag {
    font-size: 10px;
    font-weight: normal;
    color: #e6fffa;
  }

  .revisi-table {
    width: 100%;
    border-collapse: collapse;
  }

  .revisi-table td {
    padding: 4px 8px;
    border-bottom: 0.5pt solid #e2e8f0;
    vertical-align: top;
    font-size: 11px;
  }

  .revisi-table tr:last-child td {
    border-bottom: none;
  }

  .revisi-label {
    width: 28%;
    font-weight: bold;
    color: #0f172a;
  }

  .revisi-content-filled {
    color: #0f172a;
    line-height: 1.45;
    border: none !important;
  }

  .revisi-content-filled p,
  .revisi-content-filled div {
    margin: 2px 0;
    padding: 0;
    border: none !important;
  }

  .revisi-content-filled ul,
  .revisi-content-filled ol {
    margin: 2px 0 2px 18px;
    padding: 0;
    border: none !important;
  }

  .revisi-content-filled li {
    margin: 1px 0;
    border: none !important;
  }

  .revisi-content-filled span {
    border: none !important;
  }

  .revisi-content-empty {
    color: #94a3b8;
    font-style: italic;
  }

  .text-center {
    text-align: center !important;
  }

  .text-right {
    text-align: right !important;
  }

  .text-left {
    text-align: left !important;
  }

  .text-justify {
    text-align: justify !important;
  }

  .clearfix::after,
  .row::after {
    content: "";
    display: table;
    clear: both;
  }
</style>

<?php
if (!function_exists('clean_catatan_revisi')) {
  function clean_catatan_revisi($text) {
    if ($text === null) return null;
    
    // Strip Word conditional comments & Office namespace tags
    $clean_text = preg_replace('/<!--\[if.*?-->/s', '', (string)$text);
    $clean_text = preg_replace('/&lt;!--\[if.*?&gt;/s', '', $clean_text);
    $clean_text = preg_replace('/<\/?o:[a-z0-9]+>/i', '', $clean_text);
    $clean_text = preg_replace('/<\/?o>/i', '', $clean_text);
    $clean_text = preg_replace('/xss=removed/i', '', $clean_text);

    // Check if plain text after stripping tags and spaces is empty
    $plain = trim(strip_tags(html_entity_decode($clean_text, ENT_QUOTES, 'UTF-8')));
    $plain = trim(str_replace(["\xc2\xa0", '&nbsp;'], '', $plain));

    if ($plain === '' || $plain === '-' || strtolower($plain) === 'tidak ada' || strtolower($plain) === 'nihil') {
      return null;
    }

    return $clean_text;
  }
}

if (!function_exists('render_catatan_revisi')) {
  function render_catatan_revisi($text) {
    if ($text === null) return '';

    // Check if it contains HTML tags
    if (preg_match('/<[a-z][\s\S]*>/i', $text)) {
      // Allow only safe formatting structure tags (exclude span to prevent broken inline box artifacts)
      $allowed_tags = '<p><div><br><ul><ol><li><b><strong><i><em><u>';
      $cleaned = strip_tags($text, $allowed_tags);
      
      // Clean leading/trailing empty breaks and empty blocks
      $cleaned = preg_replace('/(<(p|div)[^>]*>\s*(<br\s*\/?>|&nbsp;|\s)*<\/\2>\s*)+$/i', '', $cleaned);
      $cleaned = preg_replace('/^(<(p|div)[^>]*>\s*(<br\s*\/?>|&nbsp;|\s)*<\/\2>\s*)+/i', '', $cleaned);
      $cleaned = preg_replace('/^(<(p|div)[^>]*>)\s*<br\s*\/?>/i', '$1', $cleaned);

      // Strip useless attributes (like xss=removed, class="p1", etc.) from remaining tags
      $cleaned = preg_replace('/<(p|div|ul|ol|li|b|strong|i|em|u)\s+[^>]*>/i', '<$1>', $cleaned);
      
      return trim($cleaned);
    } else {
      return nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
    }
  }
}
?>
