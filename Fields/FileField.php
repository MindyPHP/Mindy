<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Fields;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Exception;
use League\Flysystem\FilesystemInterface;
use Mindy\Orm\FileNameHasher\FileNameHasherInterface;
use Mindy\Orm\FileNameHasher\MD5NameHasher;
use Mindy\Orm\Files\File;
use Mindy\Orm\Files\LocalFile;
use Mindy\Orm\Files\ResourceFile;
use Mindy\Orm\ModelInterface;
use Mindy\Orm\OrmFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FileField.
 */
class FileField extends CharField
{
    /**
     * Upload to template, you can use these variables:
     * %Y - Current year (4 digits)
     * %m - Current month
     * %d - Current day of month
     * %H - Current hour
     * %i - Current minutes
     * %s - Current seconds
     * %O - Current object class (lower-based).
     *
     * @var string|callable|\Closure
     */
    public $uploadTo = '%M/%O/%Y-%m-%d';

    /**
     * List of allowed file types.
     *
     * @var array|null
     */
    public $mimeTypes = [];

    /**
     * @var null|int maximum file size or null for unlimited. Default value 2 mb.
     */
    public $maxSize = '5M';

    /**
     * @var callable convert file name
     */
    public $nameHasher;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @param FileNameHasherInterface $nameHasher
     */
    public function setNameHasher(FileNameHasherInterface $nameHasher)
    {
        $this->nameHasher = $nameHasher;
    }

    /**
     * @return FileNameHasherInterface
     */
    public function getNameHasher()
    {
        if ($this->nameHasher === null) {
            $this->nameHasher = new MD5NameHasher();
        }

        return $this->nameHasher;
    }

    /**
     * @return array
     */
    public function getValidationConstraints()
    {
        $constraints = [];

        $currentValue = $this->getModel()->getAttribute($this->getAttributeName());
        if ($this->isRequired() && empty($currentValue)) {
            $constraints = [
                new Assert\NotBlank(),
                new Assert\File([
                    'maxSize' => $this->maxSize,
                    'mimeTypes' => $this->mimeTypes,
                ]),
            ];
        }

        return $constraints;
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return $this->getFilesystem()->delete($this->value);
    }

    /**
     * @return int
     */
    public function size()
    {
        if (empty($this->value)) {
            return 0;
        }
        if ($this->getFilesystem()->has($this->value)) {
            /** @var \League\Flysystem\File $file */
            $file = $this->getFilesystem()->get($this->value);

            return $file->getSize();
        }

        return 0;
    }

    /**
     * @param \Mindy\Orm\Model|ModelInterface $model
     * @param $value
     */
    public function afterDelete(ModelInterface $model, $value)
    {
        if ($model->hasAttribute($this->getAttributeName())) {
            $fs = $this->getFilesystem();
            if ($fs->has($value)) {
                $fs->delete($value);
            }
        }
    }

    public function setValue($value)
    {
        if (
            is_array($value) &&
            isset($value['error']) &&
            isset($value['tmp_name']) &&
            isset($value['size']) &&
            isset($value['name']) &&
            isset($value['type'])
        ) {
            if ($value['error'] === UPLOAD_ERR_NO_FILE) {
                $value = null;
            } else {
                $value = new UploadedFile(
                    $value['tmp_name'],
                    $value['name'],
                    $value['type'],
                    (int) $value['size'],
                    (int) $value['error']
                );
            }
        } elseif (is_string($value)) {
            if (strpos($value, 'data:') !== false) {
                list($type, $value) = explode(';', $value);
                list(, $value) = explode(',', $value);
                $value = base64_decode($value);
                $value = new ResourceFile($value, null, null, $type);
            } elseif (realpath($value)) {
                $value = new LocalFile(realpath($value));
            }
        }

        if ($value === null) {
            $this->value = null;
        } elseif ($value instanceof File || $value instanceof UploadedFile) {
            $this->value = $value;
        }
    }

    /**
     * @return array|null
     */
    public function toArray()
    {
        return empty($this->value) ? null : $this->getValue();
    }

    /**
     * @return string
     */
    public function getUploadTo()
    {
        if (is_callable($this->uploadTo)) {
            return $this->uploadTo->__invoke();
        }
        $model = $this->getModel();

        return strtr($this->uploadTo, [
            '%Y' => date('Y'),
            '%m' => date('m'),
            '%d' => date('d'),
            '%H' => date('H'),
            '%i' => date('i'),
            '%s' => date('s'),
            '%O' => $model->classNameShort(),
            '%M' => $model->getBundleName(),
        ]);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof UploadedFile) {
            $value = $this->saveUploadedFile($value);
        } elseif ($value instanceof File) {
            $value = $this->saveFile($value);
        }

        if (is_string($value)) {
            $value = $this->normalizeValue($value);
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValueSQL($value, AbstractPlatform $platform)
    {
        return $value;
    }

    protected function normalizeValue($value)
    {
        return str_replace('//', '/', $value);
    }

    public function saveUploadedFile(UploadedFile $file)
    {
        $contents = file_get_contents($file->getRealPath());

        $path = $this->getNameHasher()->resolveUploadPath(
            $this->getFilesystem(),
            $this->getUploadTo(),
            $file->getClientOriginalName()
        );
        if (!$this->getFilesystem()->write($path, $contents)) {
            throw new Exception('Failed to save file');
        }

        return $path;
    }

    public function saveFile(File $file)
    {
        $contents = file_get_contents($file->getRealPath());

        $path = $this->getNameHasher()->resolveUploadPath(
            $this->getFilesystem(),
            $this->getUploadTo(),
            $file->getFilename()
        );
        if (!$this->getFilesystem()->write($path, $contents)) {
            throw new Exception('Failed to save file');
        }

        return $path;
    }

    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getFilesystem()
    {
        if (null === $this->filesystem) {
            return OrmFile::getFilesystem();
        }

        return $this->filesystem;
    }
}
