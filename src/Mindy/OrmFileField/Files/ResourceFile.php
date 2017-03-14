<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
