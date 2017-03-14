<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Tests;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Mindy\Orm\Fields\FileField;
use Mindy\Orm\FileNameHasher\DefaultHasher;
use Mindy\Orm\Files\LocalFile;
use Mindy\Orm\Files\RemoteFile;
use Mindy\Orm\Files\ResourceFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var FileField
     */
    protected $field;

    public function setUp()
    {
        $this->filesystem = new Filesystem(new Local(__DIR__ . '/temp'));
        file_put_contents(__DIR__ . '/test.txt', '123');

        $field = new FileField([
            'name' => 'file',
        ]);
        $field->setFilesystem($this->filesystem);
        $field->setNameHasher(new DefaultHasher());
        assert($field->getNameHasher() instanceof DefaultHasher);
        $field->setModel(new FileModel());

        $this->field = $field;
    }

    protected function tearDown()
    {
        foreach ($this->filesystem->listContents('/') as $file) {
            if ($file['type'] == 'dir') {
                $this->filesystem->deleteDir($file['path']);
            } else {
                $this->filesystem->delete($file['path']);
            }
        }

        $this->filesystem = null;
        $this->field = null;
    }

    public function testUploadedFile()
    {
        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $file = new UploadedFile(
            __DIR__ . '/test.txt',
            'test.txt',
            'plain/text',
            filesize(__DIR__ . '/test.txt'),
            null,
            true
        );
        $this->field->saveUploadedFile($file);

        $path = $this->field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s', date('Y-m-d')), $path);
        $this->assertEquals('123', file_get_contents(__DIR__ . '/temp/' . $path . '/test.txt'));
    }

    public function testLocalFile()
    {
        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $file = new LocalFile(__DIR__ . '/test.txt');
        $this->field->saveFile($file);

        $path = $this->field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s', date('Y-m-d')), $path);
        $this->assertEquals('123', file_get_contents(__DIR__ . '/temp/' . $path . '/test.txt'));
    }

    public function testResourceFile()
    {
        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $file = new ResourceFile('123', 'test.txt');
        $this->field->saveFile($file);

        $path = $this->field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s', date('Y-m-d')), $path);
        $this->assertEquals('123', file_get_contents(__DIR__ . '/temp/' . $path . '/test.txt'));
    }

    public function testRemoteFile()
    {
        if (@getenv('TRAVIS')) {
            $this->markTestSkipped('Skip remote file');
        }

        $file = new RemoteFile('https://raw.githubusercontent.com/MindyPHP/Mindy/master/README.md', 'readme.md');
        $this->field->saveFile($file);

        $path = $this->field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s', date('Y-m-d')), $path);
        $this->assertTrue(is_file(__DIR__ . '/temp/' . $path . '/readme.md'));
    }

    public function testFileFieldValidation()
    {
        $this->assertFalse($this->field->isValid());
        $this->assertEquals(['This value should not be blank.'], $this->field->getErrors());

        $path = __DIR__ . '/test.txt';
        file_put_contents($path, '123');

        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $uploadedFile = new UploadedFile(__FILE__, 10000000, UPLOAD_ERR_OK, basename(__FILE__), 'text/php');
        $this->field->setValue($uploadedFile);
        $this->assertFalse($this->field->isValid());
        $this->assertEquals(['The file could not be uploaded.'], $this->field->getErrors());

        $this->field->setValue($path);
        $this->assertInstanceOf(LocalFile::class, $this->field->getValue());
        $this->assertTrue($this->field->isValid());

        $this->field->mimeTypes = [
            'image/*',
        ];

        $uploadedFile = new LocalFile('qweqwe', false);
        $this->field->setValue($uploadedFile);
        $this->assertFalse($this->field->isValid());
        $this->assertEquals('The file could not be found.', $this->field->getErrors()[0]);

        $uploadedFile = new ResourceFile(base64_encode(file_get_contents(__FILE__)));
        $this->field->setValue($uploadedFile);
        $this->assertFalse($this->field->isValid());
        $this->assertEquals('The mime type of the file is invalid ("text/plain"). Allowed mime types are "image/*".',
            $this->field->getErrors()[0]);

        @unlink($path);
    }

    public function testResourceField()
    {
        $resource = new ResourceFile(base64_encode(file_get_contents(__FILE__)), 'test.php');
        $this->field->setValue($resource);
        $this->assertTrue($this->field->isValid());

        $this->field->saveFile($resource);

        $path = $this->field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s', date('Y-m-d')), $path);
        $this->assertTrue(is_file(__DIR__ . '/temp/' . $path . '/test.php'));
    }

    public function testResourceFieldNoHasher()
    {
        $resource = new ResourceFile(base64_encode(file_get_contents(__FILE__)), 'test.php');
        $this->field->setValue($resource);
        $this->assertTrue($this->field->isValid());

        $this->field->saveFile($resource);

        $path = $this->field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s', date('Y-m-d')), $path);
        $this->assertTrue(is_file(__DIR__ . '/temp/' . $path . '/test.php'));
    }
}
