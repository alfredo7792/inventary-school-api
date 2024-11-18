<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary' => 'wkhtmltopdf', // Ruta al binario wkhtmltopdf si no está en el PATH
        'timeout' => 120,
        'options' => [
            'defaultFont' => 'Arial', // Fuente por defecto
            'isRemoteEnabled' => true, // Habilita carga de URLs
            'isHtml5ParserEnabled' => true, // Habilita el parser HTML5
            'isChunked' => true, // Habilita la salida por partes
            'isPhpEnabled' => true, // Habilita la ejecución de PHP
            'isFontSubsettingEnabled' => true, // Habilita el subconjunto de fuentes
            'dpi' => 96, // Resolución del PDF
        ],
    ],
];