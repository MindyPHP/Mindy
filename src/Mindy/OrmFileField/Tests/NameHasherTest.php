<?php
/**
 * Created by IntelliJ IDEA.
 * User: max
 * Date: 14/03/2017
 * Time: 19:59
 */

namespace Mindy\Orm\Tests;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Mindy\Orm\FileNameHasher\DefaultHasher;
use Mindy\Orm\FileNameHasher\MD5NameHasher;

class NameHasherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    public function setUp()
    {
        $this->filesystem = new Filesystem(new Local(__DIR__.'/temp'));
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

    public function testDefaultNameHasherAvailable()
    {
        $path = sys_get_temp_dir();
        $expectedName = sprintf("%s/NameHasherTest.php", ltrim($path, '/'));
        $resolvedName = (new DefaultHasher())->resolveUploadPath($this->filesystem, $path, basename(__FILE__));
        $this->assertSame($expectedName, $resolvedName);
        $this->assertFalse($this->filesystem->has($resolvedName));
    }

    public function testDefaultNameHasherTaken()
    {
        $resolvedName = (new DefaultHasher())->resolveUploadPath($this->filesystem, '/', basename(__FILE__));
        $this->assertSame(
            "/NameHasherTest.php",
            $resolvedName
        );

        $this->filesystem->write("/NameHasherTest.php", '123');
        $this->filesystem->has('/NameHasherTest.php');

        $resolvedName = (new DefaultHasher())->resolveUploadPath($this->filesystem, '/', basename(__FILE__));
        $this->assertSame('/NameHasherTest_1.php', $resolvedName);

        $this->filesystem->write($resolvedName, '123');
        $this->assertTrue($this->filesystem->has($resolvedName));
    }

    public function testMD5NameHasher()
    {
        $hasher = new MD5NameHasher();
        $this->assertSame(
            "/6f997ff817ae3852abfaa147e5d63aa7.php",
            $hasher->resolveUploadPath($this->filesystem, '/', md5(basename(__FILE__)) . '.php')
        );
    }
}
