<?php

if (php_sapi_name() !== 'cli') {
    echo "ERROR: This script should be executed from the command line.\n";
    return;
}

// Check if a certain branch is given
if (!isset($argv[1])) {
    echo "ERROR: No specific version\n";
    return;
} else {
    $version = $argv[1];
}

$paths = array_map(function ($item) {
    return $item['path'];
}, json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'components.json'), true));

function updateVersion($path, $vers) {
    $json = json_decode(file_get_contents($path), true);
    $json['extra']['branch-alias']['dev-master'] = sprintf("%s-dev", $vers);
    return json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}

foreach ($paths as $path) {
    $realpath = realpath(__DIR__ . '/../' . $path);
    if (file_exists($realpath . '/composer.json')) {
        $newJson = updateVersion($realpath . '/composer.json', $version);
        file_put_contents($realpath . '/composer.json', $newJson);
    } else {
        echo sprintf("ERROR: Missing composer.json in %s\n", $path);
    }
}

$newJson = updateVersion(__DIR__ . '/../composer.json', $version);
file_put_contents(__DIR__ . '/../composer.json', $newJson);