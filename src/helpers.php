<?php

if (!function_exists('env')) {
    function env(string $varname, $default = null)
    {
        return $_ENV[$varname] ?? $default;
    }
}
