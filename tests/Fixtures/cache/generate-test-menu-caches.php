<?php

//
// A simple command to generate test menu caches
//

$menus = require __DIR__ . '/../config/test-menus.php';

foreach ($menus as $name => $menu) {
    file_put_contents(__DIR__ . '/' . $name . '.cache', serialize($menu));
}
