<?php

function config(string $key, $default = null)
{
    $parts = explode('.', $key);
    $file = __DIR__ . '/../../Config/' . $parts[0] . '.php';

    if (!file_exists($file)) {
        file_put_contents(__DIR__ . '/../../logs/app.log', ["File not exists in ", $file, '           \n'], FILE_APPEND);

        return $default;
    }

    $config = require $file;

    return $parts[1] ?? null ? ($config[$parts[1]] ?? $default) : $config;
}
