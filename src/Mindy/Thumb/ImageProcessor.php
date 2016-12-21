<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/09/16
 * Time: 14:03.
 */

namespace Mindy\Thumb;

use Exception;
use Imagine\Image\ImageInterface;

/**
 * Class ImageProcessor.
 */
class ImageProcessor extends AbstractProcessor implements ImageProcessorInterface
{
    use ImageProcess;

    /**
     * @var bool
     */
    public $storeOriginal = true;
    /**
     * Default resize method.
     *
     * @var string
     */
    public $defaultResize = 'adaptiveResizeFromTop';
    /**
     * Array with image sizes
     * key 'original' is reserved!
     * example:
     * [
     *      'thumb' => [
     *          300,200,
     *          'method' => 'adaptiveResize'
     *      ]
     * ].
     *
     * There are 3 methods resize(THUMBNAIL_INSET), adaptiveResize(THUMBNAIL_OUTBOUND),
     * adaptiveResizeFromTop(THUMBNAIL_OUTBOUND from top)
     *
     * @var array
     */
    public $sizes = [];
    /**
     * @var array
     */
    protected $availableResizeMethods = [
        'resize',
        'adaptiveResize',
        'adaptiveResizeFromTop',
    ];
    /**
     * @var string
     */
    protected $uploadTo;

    /**
     * ImageProcessor constructor.
     *
     * @param array $config
     *
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @param array $options
     *
     * @return array
     *
     * @throws Exception
     */
    protected function findOptionsByConfigPart(array $options)
    {
        $sizes = $this->getSizes();

        if (isset($options['name'])) {
            foreach ($sizes as $config) {
                if (isset($config['name']) && $config['name'] == $options['name']) {
                    return $config;
                }
            }
        } else {
            foreach ($sizes as $config) {
                $configWidth = isset($config['width']) ? $config['width'] : '';
                $configHeight = isset($config['height']) ? $config['height'] : '';
                $optionsWidth = isset($options['width']) ? $options['width'] : '';
                $optionsHeight = isset($options['height']) ? $options['height'] : '';

                $configSize = sprintf('%sx%s', $configWidth, $configHeight);
                $optionsSize = sprintf('%sx%s', $optionsWidth, $optionsHeight);
                if ($configSize == $optionsSize) {
                    return $config;
                }
            }
        }

        throw new Exception('Cannot find options for: '.print_r($options, true));
    }

    /**
     * @param string $value
     * @param array  $config
     *
     * @return string
     *
     * @throws Exception
     */
    public function path($value, array $config = [])
    {
        $options = $this->findOptionsByConfigPart($config);

        $force = isset($options['force']) ? $options['force'] : false;
        $checkMissing = isset($options['checkMissing']) ? $options['checkMissing'] : false;
        if ($force || ($checkMissing && !$this->has($value))) {
            $this->process($value);
        }

        return $value;
    }

    /**
     * @param string $value
     * @param array  $config
     *
     * @return string
     */
    public function url($value, array $config = [])
    {
        $options = $this->findOptionsByConfigPart($config);

        if (
            (isset($options['force']) && $options['force']) ||
            (isset($options['checkMissing']) && $options['checkMissing'] && $this->has($this->generateFilename($value, $options)) == false)
        ) {
            $contents = $this->getFilesystem()->read($value);
            $image = $this->getImagine()->load($contents);
            $this->processSource($value, $image, null);
        }

        $fileName = $this->generateFilename($value, $options);

        return ltrim($fileName, '/');
    }

    /**
     * @param string         $path
     * @param ImageInterface $image
     * @param null           $prefix
     *
     * @return $this
     *
     * @throws Exception
     */
    public function processSource($path, ImageInterface $image, $prefix = null)
    {
        $defaultConfig = [
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 72,
            'resolution-y' => 72,
            'jpeg_quality' => 100,
            'quality' => 100,
            'png_compression_level' => 0,
        ];

        foreach ($this->getSizes() as $config) {

            /*
             * Skip unused sizes
             */
            if (is_null($prefix) === false && $config['name'] !== $prefix) {
                continue;
            }

            $width = isset($config['width']) ? $config['width'] : null;
            $height = isset($config['height']) ? $config['height'] : null;
            $method = isset($config['method']) ? $config['method'] : $this->defaultResize;
            $extSize = isset($config['format']) ? $config['format'] : pathinfo($path, PATHINFO_EXTENSION);

            if (!in_array($method, $this->availableResizeMethods)) {
                throw new Exception('Unknown resize method: '.$method);
            }

            if (!$width || !$height) {
                list($width, $height) = $this->imageScale($image, $width, $height);
            }

            $newSource = $this->resize($image->copy(), $width, $height, $method);
            if (isset($config['watermark'])) {
                $watermarkConfig = $config['watermark'];
                if (!isset($watermarkConfig['file']) || !is_file($watermarkConfig['file'])) {
                    throw new Exception('Watermark image missing or not exists');
                }

                $watermark = self::getImagine()->open($watermarkConfig['file']);
                $newSource = $this->applyWatermark(
                    $newSource, $watermark,
                    isset($watermarkConfig['position']) ? $watermarkConfig['position'] : 'center'
                );
            }

            $sizePath = $this->generateFilename($path, $config);
            if ($this->has($sizePath)) {
                $this->getFilesystem()->delete($sizePath);
            }
            $this->write($sizePath, $newSource->get($extSize, isset($config['config']) ? $config['config'] : $defaultConfig));
        }

        if ($this->storeOriginal === false) {
            $originalPath = $this->uploadTo.DIRECTORY_SEPARATOR.basename($path);
            if ($this->has($originalPath)) {
                $this->getFilesystem()->delete($originalPath);
            }
        }

        return $this;
    }

    /**
     * @param string $path
     * @param array  $options
     *
     * @return string
     */
    public function generateFilename($path, array $options = [])
    {
        ksort($options);
        $serialized = serialize($options);
        $hash = substr(md5($serialized), 0, 10);
        $name = basename($path);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $basename = substr($name, 0, strpos($name, $ext) - 1);

        return rtrim($this->uploadTo, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.implode('_', [$basename, $hash]).'.'.$ext;
    }

    /**
     * @param string $path
     * @param null   $prefix
     *
     * @return $this
     *
     * @throws Exception
     */
    public function process($path, $prefix = null)
    {
        if (!is_file($path)) {
            throw new Exception('File not found: '.$path);
        }

        $image = $this->getImagine()->open($path);

        return $this->processSource($path, $image, $prefix);
    }
}
