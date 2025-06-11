<?php
function base_url($path = '')
{
    return ($path ? '/' . ltrim($path, '/') : '');
}
