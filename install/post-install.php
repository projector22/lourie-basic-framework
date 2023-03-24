<?php

$install_path = 'node_modules';

$path = realpath(__DIR__ . '/..');

$cmd = "npm install --omit=dev --prefix {$path}";

passthru($cmd);

echo "\n";
