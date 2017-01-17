<?php

if (php_sapi_name() !== 'cli') {
    echo "ERROR: This script should be executed from the command line.\n";
    return;
}

// Check if a certain branch is given
if (!isset($argv[1])) {
    if (!confirm('No specific branch selected, continue with master branch?')) {
        return;
    }
    $branch = 'master';
} else {
    $branch = $argv[1];
}

// Check if the components should be tagged or not
if (!isset($argv[2])) {
    echo "Component is missing\n";
    return;
} else {
    $subtree = $argv[2];
}

$components = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'components.json'));

// Push main project first
echo "> Pushing parent repository ...\n";
echo 'git push origin ' . $branch . "\n\n";
exec('git push origin ' . $branch);

foreach ($components as $component) {
    if ($component->path == $subtree) {
        if (!is_dir(getcwd() . DIRECTORY_SEPARATOR . $component->path)) {
            echo "> Subtree '" . $component->path . "' not existing for component " . $component->path . ". Please check the component mappings.\n";
            continue;
        }

        echo '> Pushing subtree ' . $component->path . ' to ' . $component->git . ' (branch ' . $branch . ")\n";
        echo 'git subtree push --prefix=' . $component->path . ' ' . $component->git . ' ' . $branch . "\n\n";
        exec('git subtree push --prefix=' . $component->path . ' --squash ' . $component->git . ' ' . $branch);
    }
}

function confirm($question)
{
    while (true) {
        if (PHP_OS == 'WINNT') {
            echo '$> ' . $question . ' [Y/n] ';
            $line = strtolower(trim(stream_get_line(STDIN, 1024, PHP_EOL)));
        } else {
            $line = strtolower(trim(readline('$> ' . $question . ' [Y/n] ')));
        }

        if (!$line || $line == 'y') {
            return true;
        } elseif ($line == 'n') {
            return false;
        }
    }
}
