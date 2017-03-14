<?php

$packages = [
    [
        'remote_url' => 'git@github.com:MindyPHP/FileBundle.git',
        'remote_name' => 'file-bundle',
        'path' => 'src/Mindy/Bundle/FileBundle',
    ]
];

function cmd($command) {
    echo $command . PHP_EOL;
    exec($command);
}

function recreateSubtree($part, $branch = 'master') {
    $commands = [
        // Clenaup
        "\n\necho Cleanup\n\n",
        'git remote rm {remote_name}',
        'git branch -D {remote_name}',

        "\n\necho Create\n\n",
        // Create
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

foreach ($packages as $part) {
    recreateSubtree($part, 'subtree');
}
