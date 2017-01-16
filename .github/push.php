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
    $tag = null;
} else {
    $tag = $argv[2];
}

$components = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'components.json'));

// Push main project first
echo "> Pushing parent repository ...\n";
echo 'git push origin ' . $branch . "\n\n";
exec('git push origin ' . $branch);

// Evaluate tag
if (null !== $tag) {
    echo "> Creating tag " . $tag . " for main repository\n";
    echo 'git tag -a ' . $tag . ' -m "Version ' . $tag . '"' . "\n\n";
    exec('git tag -a ' . $tag . ' -m "Version ' . $tag . '"');

    echo "> Pushing tag to main repository\n";
    echo "git push origin --tags\n\n";
    exec('git push origin --tags');

    echo "> Deleting tag\n";
    echo 'git tag -d ' . $tag . "\n\n";
    exec('git tag -d ' . $tag);
}

// Push each component to its component repository
foreach ($components as $component) {
    if (!is_dir(getcwd() . DIRECTORY_SEPARATOR . $component->path)) {
        echo "> Subtree '" . $component->path . "' not existing for component " . $component->path . ". Please check the component mappings.\n";
        continue;
    }

    echo '> Pushing subtree ' . $component->path . ' to ' . $component->git . ' (branch ' . $branch . ")\n";
    echo 'git subtree push --prefix=' . $component->path . ' ' . $component->git . ' ' . $branch . "\n\n";
    exec('git subtree push --prefix=' . $component->path . ' --squash ' . $component->git . ' ' . $branch);

    // Evaluate tag
    if (null !== $tag) {
        $temporaryBranch = 'component-split';

        echo "> Splitting component into a temporary branch '" . $temporaryBranch . "'\n";
        echo 'git subtree split --prefix=' . $component->path . ' -b ' . $temporaryBranch . "\n\n";
        exec('git subtree split --prefix=' . $component->path . ' -b ' . $temporaryBranch);

        echo "> Creating tag " . $tag . " for component " . $component->path . " in branch '" . $temporaryBranch . "'\n";
        echo 'git tag -a ' . $tag . ' -m "Version ' . $tag . '" ' . $temporaryBranch . "\n\n";
        exec('git tag -a ' . $tag . ' -m "Version ' . $tag . '" ' . $temporaryBranch);

        echo "> Pushing tag to component repository\n";
        echo 'git push ' . $component->git . " --tags\n\n";
        exec('git push ' . $component->git . ' --tags');

        echo "> Removing temporary branch '" . $temporaryBranch . "'\n";
        echo 'git branch -D ' . $temporaryBranch . "\n\n";
        exec('git branch -D ' . $temporaryBranch);

        echo "> Removing tag\n";
        echo 'git tag -d ' . $tag . "\n\n";
        exec('git tag -d ' . $tag);
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
