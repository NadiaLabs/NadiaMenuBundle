<?php

$container->loadFromExtension('nadia_menu', [
    'cache' => require __DIR__ . '/test-cache.php',
    'menus' => require __DIR__ . '/test-menus.php',
    'menu_providers' => require __DIR__ . '/test-menus-providers.php',
]);
