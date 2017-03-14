<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Files;

/**
 * Class ResourceFile.
 */
class ResourceFile extends File
{
    /**
     * ResourceFile constructor.
     *
     * @param string      $content
     * @param null|string $name
     * @param null|string $tempDir
     */
    public function __construct($content, $name = null, $tempDir = null)
    {
        $path = tempnam($tempDir ?: sys_get_temp_dir(), 'tmp');

        if ($name) {
            $path = dirname($path).DIRECTORY_SEPARATOR.$name;
        }

        file_put_contents($path, $content);

        parent::__construct($path);
    }
}
