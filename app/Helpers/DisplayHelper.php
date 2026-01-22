<?php

if (! function_exists('clean_number')) {
    function clean_number($value)
    {
        if ($value === null) {
            return '';
        }

        return rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
    }
}
