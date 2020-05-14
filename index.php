<?php
$xml = simplexml_load_file("textes.xml");
$fontspec = array();
foreach ($xml->page as $p) {
    $textes = '';
    $data = array();
    $data_json = array();
    foreach ($p->fontspec as $fspec) {
        $tmp = (array) $fspec->attributes();
        $fontspec[(string) $fspec['id']] = $tmp['@attributes'];
    }
    foreach ($p->text as $t) {
        $text_as_xml = $t->asXml();
        $ps = strpos($text_as_xml, '>') + 1;
        $textes = substr($text_as_xml, $ps, -7);
        $x = 100 * $t['left'] / $p['width'];
        $y = 100 * $t['top'] / $p['height'];
        $w = 100 * $t['width'] / $p['width'];
        $h = 100 * $t['height'] / $p['height'];
        $data['x'] = round($x, 6) . '%';
        $data['y'] = round($y, 6) . '%';
        $data['w'] = round($w, 6) . '%';
        $data['h'] = round($h, 6) . '%';
        $data['txt'] = $textes;
        $id = (int) $t['font'];
        if (isset($fontspec[$id])) {
            $family = $fontspec[$id]['family'];
            $color = $fontspec[$id]['color'];
            $size = $fontspec[$id]['size'];
            $style = "font-family: $family; color: $color; font-size: $size;";
            $data['style'] = $style;
        }
        array_push($data_json, $data);
    }
    $data_json = json_encode($data_json, JSON_UNESCAPED_UNICODE);
    $data_json = str_replace('<\/', '</', $data_json);
    $fn = "page_$p[number].json";
    file_put_contents($fn, $data_json);
}
