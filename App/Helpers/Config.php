<?php

function config(string $key, $default = null)
{
    $parts = explode('.', $key);
    $file = dirname(__DIR__, 2) . '/logs/' . $parts[0] . '.log';

    if (!file_exists($file)) {
        file_put_contents(dirname(__DIR__, 2) . '/logs/app.log', ["File not exists in ", $file, '           \n'], FILE_APPEND);

        return $default;
    }

    $config = require $file;

    return $parts[1] ?? null ? ($config[$parts[1]] ?? $default) : $config;
}
