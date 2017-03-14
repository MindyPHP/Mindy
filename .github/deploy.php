<?php

$packages = [
    [
        'remote_url' => 'git@github.com:MindyPHP/FileBundle.git',
        'remote_name' => 'file-bundle',
        'path' => 'src/Mindy/Bundle/FileBundle',
    ],
    [
        'remote_url' => 'git@github.com:MindyPHP/AdminBundle.git',
        'remote_name' => 'admin-bundle',
        'path' => 'src/Mindy/Bundle/AdminBundle',
    ]
];

// Check if a certain branch is given
if (!isset($argv[1])) {
    if (!confirm('No specific package selected')) {
        return;
    }
    $package = 'master';
} else {
    $package = $argv[1];
}

// Check if a certain branch is given
if (!isset($argv[2])) {
    if (!confirm('No specific branch selected, continue with master branch?')) {
        return;
    }
    $branch = 'master';
} else {
    $branch = $argv[2];
}

function cmd($command) {
    echo $command . PHP_EOL;
    $out = [];
    exec($command, $out, $return);
    echo implode("\n", $out);
    return $return;
}

function recreateSubtree($part, $branch = 'master') {
    $commands = [
        // Clenaup
        "\n\necho Cleanup\n\n",
        'git remote rm {remote_name}',
        'git branch -D {remote_name}',

        "\n\necho Create\n\n",
        // Create
        'git checkout -b {branch}',
        'git remote add {remote_name} {remote_url}',
        'git fetch {remote_name}',
        'git checkout -b {remote_name} {remote_name}/master',
        'git checkout {branch}',
        'git rm -rf {path}',
        'git commit -am "subtree up"',
        'git read-tree --prefix={path} -u {remote_name}',
        'git commit -am "subtree up"',
        'git push origin {branch}',
    ];
    foreach ($commands as $command) {
        cmd(strtr($command, [
            '{branch}' => $branch,
            '{path}' => $part['path'],
            '{remote_name}' => $part['remote_name'],
            '{remote_url}' => $part['remote_url']
        ]));
    }
}

function gitStatus() {
    return cmd('git diff-index --quiet HEAD --') == 0;
}

if (gitStatus()) {
    foreach ($packages as $part) {
        if ($part['remote_name'] == $package) {
            recreateSubtree($part, $branch);
        }
    }
} else {
    echo "Commit your working tree" . PHP_EOL;
}
