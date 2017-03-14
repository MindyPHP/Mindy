<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Template\Adapter;

use RuntimeException;

/**
 * Class FileAdapter.
 */
class FileAdapter implements Adapter
{
    /**
     * @var array
     */
    protected $source;

    /**
     * FileAdapter constructor.
     *
     * @param $source
     */
    public function __construct($source)
    {
        if (!is_array($source)) {
            $path = realpath($source);
            if (!$path) {
                throw new RuntimeException(sprintf('source directory %s not found', $source));
            }
            $paths = [$path];
        } else {
            $paths = [];
            foreach ($source as $path) {
                if ($absPath = realpath($path)) {
                    $paths[] = $absPath;
                } else {
                    throw new RuntimeException(sprintf('source directory %s not found', $path));
                }
            }
        }
        $this->source = $paths;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function isReadable($path)
    {
        foreach ($this->source as $source) {
            if (is_readable($source.'/'.$path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $path
     *
     * @return int|null
     */
    public function lastModified($path)
    {
        foreach ($this->source as $source) {
            if (is_file($source.'/'.$path)) {
                return filemtime($source.'/'.$path);
            }
        }
    }

    /**
     * @param $path
     *
     * @return null|string
     */
    public function getContents($path)
    {
        foreach ($this->source as $source) {
            if (is_file($source.'/'.$path)) {
                return file_get_contents($source.'/'.$path);
            }
        }
    }
}
