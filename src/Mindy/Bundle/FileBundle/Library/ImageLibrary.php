<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\FileBundle\Library;

use Exception;
use Imagine\Image\ImageInterface;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Mindy\Template\Library;
use Mindy\Thumb\ImageProcess;

/**
 * Class ImageLibrary.
 */
class ImageLibrary extends Library
{
    use ImageProcess;

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
     * @param CacheManager $cacheManager
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
            'thumb' => function ($path, $width, $height = null, $method = 'adaptiveResize', $watermark = [], $options = []) {
                if (empty($path)) {
                    return sprintf('http://placehold.it/%sx%s', $width, $height);
                }

                $generateConfig = [$path, $width, $height, $method, $watermark, $options];
                $newPath = $this->generateFilename($path, $generateConfig);
                if ($this->filesystem->has($newPath) == false) {
                    $newPath = $this->process($path, $width, $height, $method, $watermark, $options);
                }

                return $newPath;
            },
            'imagine_filter' => function ($path, $filter, array $runtimeConfig = [], $resolver = null) {
                if (null === $this->cacheManager) {
                    throw new \RuntimeException('Missing CacheManager');
                }
                return $this->cacheManager->getBrowserPath($path, $filter, $runtimeConfig, $resolver);
            },
        ];
    }

    protected function process($path, $width, $height = null, $method = 'adaptiveResize', $watermark = [], $options = [])
    {
        static $resizeMethods = ['adaptiveResize', 'resize', 'adaptiveResizeFromTop'];

        $generateConfig = [$path, $width, $height, $method, $watermark, $options];

        $options = array_merge([
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 72,
            'resolution-y' => 72,
            'jpeg_quality' => 100,
            'quality' => 100,
            'png_compression_level' => 0,
        ], $options);

        if (!in_array($method, $resizeMethods)) {
            throw new Exception('Unknown resize method: '.$method);
        }

        $file = $this->filesystem->get($path);

        $imagine = self::getImagine();
        $image = $imagine->load($file->read());

        if (!$width || !$height) {
            list($width, $height) = $this->imageScale($image, $width, $height);
        }

        $newSource = $this->resize($image->copy(), $width, $height, $method);
        if (!empty($watermark)) {
            if (is_array($watermark)) {
                list($watermarkFile, $watermarkPosition) = $watermark;
            } else {
                $watermarkFile = $watermark;
                $watermarkPosition = 'center';
            }
            $watermark = $imagine->open($watermarkFile);
            $newSource = $this->applyWatermark($newSource, $watermark, $watermarkPosition);
        }

        $sizePath = $this->generateFilename($path, $generateConfig);
        $this->filesystem->write($sizePath, $newSource->get(pathinfo($path, PATHINFO_EXTENSION), $options));

        return $sizePath;
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return string
     */
    public function generateFilename($path, array $options = [])
    {
        $hash = md5(json_encode($options));
        $newFilename = implode('_', [pathinfo($path, PATHINFO_FILENAME), $hash]).'.'.pathinfo($path, PATHINFO_EXTENSION);

        return pathinfo($path, PATHINFO_DIRNAME).'/'.$newFilename;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
