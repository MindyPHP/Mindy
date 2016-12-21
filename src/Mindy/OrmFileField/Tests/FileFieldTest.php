<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/12/2016
 * Time: 13:15.
 */

namespace Mindy\Orm\Tests;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Mindy\Orm\Fields\FileField;
use Mindy\Orm\Files\LocalFile;
use Mindy\Orm\Files\RemoteFile;
use Mindy\Orm\Files\ResourceFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileFieldTest extends \PHPUnit_Framework_TestCase
{
    protected $filesystem;

    public function setUp()
    {
        $this->filesystem = new Filesystem(new Local(__DIR__.'/temp'));
        file_put_contents(__DIR__.'/test.txt', '123');
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
    }

    public function testUploadedFile()
    {
        $field = new FileField();
        $field->setFilesystem($this->filesystem);

        $field->setModel(new FileModel());

        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $file = new UploadedFile(
            __DIR__.'/test.txt',
            'test.txt',
            'plain/text',
            filesize(__DIR__.'/test.txt'),
            null,
            true
        );
        $field->saveUploadedFile($file);

        $path = $field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s/', date('Y-m-d')), $path);
        $this->assertEquals('123', file_get_contents(__DIR__.'/temp/'.$path.'test.txt'));
    }

    public function testLocalFile()
    {
        $field = new FileField();
        $field->setFilesystem($this->filesystem);

        $field->setModel(new FileModel());

        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $file = new LocalFile(__DIR__.'/test.txt');
        $field->saveFile($file);

        $path = $field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s/', date('Y-m-d')), $path);
        $this->assertEquals('123', file_get_contents(__DIR__.'/temp/'.$path.'test.txt'));
    }

    public function testResourceFile()
    {
        $field = new FileField();
        $field->setFilesystem($this->filesystem);

        $field->setModel(new FileModel());

        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $file = new ResourceFile('123', 'test.txt');
        $field->saveFile($file);

        $path = $field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s/', date('Y-m-d')), $path);
        $this->assertEquals('123', file_get_contents(__DIR__.'/temp/'.$path.'test.txt'));
    }

    public function testRemoteFile()
    {
        if (@getenv('TRAVIS')) {
            $this->markTestSkipped('Skip remote file');
        }

        $field = new FileField();
        $field->setFilesystem($this->filesystem);

        $field->setModel(new FileModel());

        $file = new RemoteFile('https://raw.githubusercontent.com/MindyPHP/Mindy/master/readme.md', 'readme.md');
        $field->saveFile($file);

        $path = $field->getUploadTo();
        $this->assertEquals(sprintf('foo/FileModel/%s/', date('Y-m-d')), $path);
        $this->assertTrue(is_file(__DIR__.'/temp/'.$path.'readme.md'));
    }

    public function testFileFieldValidation()
    {
        $model = new FileModel();

        $field = new FileField([
            'name' => 'file',
        ]);
        $field->setModel($model);
        $this->assertFalse($field->isValid());
        $this->assertEquals(['This value should not be blank.'], $field->getErrors());

        $path = __DIR__.'/test.txt';
        file_put_contents($path, '123');

        // $path, $originalName, $mimeType = null, $size = null, $error = null, $test = false
        $uploadedFile = new UploadedFile(__FILE__, 10000000, UPLOAD_ERR_OK, basename(__FILE__), 'text/php');
        $field->setValue($uploadedFile);
        $this->assertFalse($field->isValid());
        $this->assertEquals(['The file could not be uploaded.'], $field->getErrors());

        $field->setValue($path);
        $this->assertInstanceOf(LocalFile::class, $field->getValue());
        $this->assertTrue($field->isValid());

        $field = new FileField([
            'mimeTypes' => [
                'image/*',
            ],
            'name' => 'file',
        ]);
        $field->setModel($model);

        $uploadedFile = new LocalFile('qweqwe', false);
        $field->setValue($uploadedFile);
        $this->assertFalse($field->isValid());
        $this->assertEquals('The file could not be found.', $field->getErrors()[0]);

        $uploadedFile = new ResourceFile(base64_encode(file_get_contents(__FILE__)));
        $field->setValue($uploadedFile);
        $this->assertFalse($field->isValid());
        $this->assertEquals('The mime type of the file is invalid ("text/plain"). Allowed mime types are "image/*".', $field->getErrors()[0]);

        @unlink($path);
    }
}
