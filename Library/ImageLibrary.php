<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\FileBundle\Library;

use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Mindy\Template\Library;

/**
 * Class ImageLibrary.
 */
class ImageLibrary extends Library
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * ImageLibrary constructor.
     *
     * @param FilesystemInterface $filesystem
     * @param CacheManager        $cacheManager
     */
    public function __construct(FilesystemInterface $filesystem, CacheManager $cacheManager = null)
    {
        $this->filesystem = $filesystem;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'imagine_filter' => function ($path, $filter, array $runtimeConfig = [], $resolver = null) {
                if (null === $this->cacheManager) {
                    throw new \RuntimeException('Missing CacheManager');
                }

                return $this->cacheManager->getBrowserPath($path, $filter, $runtimeConfig, $resolver);
            },
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
