<?php

function module_path(string $path = ''): string
{
    return base_path('modules' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
}
