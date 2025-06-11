<?php
// if (!function_exists('dd')) {
//     function dd(...$vars) {
//         echo "<pre style='background: #2d2d2d; color: #ffffff; padding: 10px; border-radius: 5px; font-size: 14px; line-height: 1.5;'>";
//         foreach ($vars as $var) {
//             print_r($var);
//             echo "\n----------------------\n";
//         }
//         echo "</pre>";
//         die();
//     }
// }


if (!function_exists('dd')) {
    function dd(...$vars) {
        foreach ($vars as $var) {
            dump($var); // Uses Symfony's VarDumper if available
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    function dump(...$vars) {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
    }
}