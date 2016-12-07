<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 20/09/16
 * Time: 15:13
 */

namespace Mindy\Thumb\Tests;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Mindy\Thumb\ImageProcessor;

class ImageProcessorTest extends \PHPUnit_Framework_TestCase
{
    protected $filesystem;

    public function setUp()
    {
        $this->filesystem = new Filesystem(new Local(__DIR__ . '/temp'));
    }

    public function tearDown()
    {
        foreach ($this->filesystem->listContents('/') as $file) {
            $this->filesystem->delete($file['path']);
        }
    }

    public function testGeneratePath()
    {
        $file = __FILE__;
        $processor = new ImageProcessor();
        $processor->setFilesystem($this->filesystem);
        $this->assertEquals('ImageProcessorTest_cef4527f9f.php', basename($processor->generateFilename($file, ['width' => 100])));
        $this->assertEquals('ImageProcessorTest_e0bcc3e26e.php', basename($processor->generateFilename($file, ['height' => null, 'width' => 100])));
        $this->assertEquals('ImageProcessorTest_e0bcc3e26e.php', basename($processor->generateFilename($file, ['width' => 100, 'height' => null])));
    }

    public function testResize()
    {
        $options = [
            'name' => 'thumb',
            'width' => 200,
            'height' => null,
            'options' => [
                'jpeg_quality' => 100,
                'quality' => 100,
            ]
        ];
        $processor = new ImageProcessor([
            'uploadTo' => '/',
            'storeOriginal' => false,
            'sizes' => [
                $options
            ]
        ]);
        $processor->setFilesystem($this->filesystem);

        $fileName = $processor->generateFilename('/temp/cat.jpg', $options);
        $this->assertEquals('cat_0343c5aa39.jpg', basename($fileName));

        $processor->process(__DIR__ . '/cat.jpg');

        /** @var \League\Flysystem\FilesystemInterface $fs */
        $this->assertEquals(1, count($this->filesystem->listContents('/')));
        $this->assertEquals('cat_0343c5aa39.jpg', $processor->url('/temp/cat.jpg', ['name' => 'thumb']));
        $this->assertEquals('cat_0343c5aa39.jpg', $processor->url('/temp/cat.jpg', ['width' => 200]));
    }

    public function testWatermark()
    {
        $options = [
            'name' => 'thumb',
            'width' => 200,
            'height' => null,
            'options' => [
                'jpeg_quality' => 100,
                'quality' => 100,
            ],
            'watermark' => [
                'file' => __DIR__ . '/watermark.png',
                'position' => 'repeat'
            ]
        ];
        $processor = new ImageProcessor([
            'uploadTo' => '/',
            'storeOriginal' => false,
            'sizes' => [
                $options
            ]
        ]);
        $processor->setFilesystem($this->filesystem);
        $processor->process(__DIR__ . '/cat.jpg');

        $this->assertEquals(1, count($this->filesystem->listContents('/')));
    }
}
