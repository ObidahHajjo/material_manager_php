<?php
function base_url($path = '')
{
    return "http://localhost:8080" . ($path ? '/' . ltrim($path, '/') : '');
}
