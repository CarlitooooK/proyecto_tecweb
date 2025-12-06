<?php

function assignLogoByExtension(string $filename): string {

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $icons = [
        'pdf' => 'img/pdf.png',
        'zip' => 'img/zip.png',
        'json' => 'img/json.png',
        'xml' => 'img/xml.png',
        'exe' => 'img/exe.png',
        'jar' => 'img/jar.png',
    ];

    return $icons[$ext] ?? 'img/default.png';
}
?>
