<?php

declare(strict_types=1);

spl_autoload_register(static function (string $fqcn) {
    $path = str_replace('\\', '/', $fqcn) . '.php';
    require_once($path);
});
