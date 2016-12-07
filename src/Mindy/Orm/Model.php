<?php

namespace Mindy\Orm;

use ReflectionClass;

/**
 * Class Model
 * @package Mindy\Orm
 */
class Model extends AbstractModel
{
    use LegacyMethodsTrait;

    /**
     * @return string
     */
    public static function tableName()
    {
        $bundleName = self::getBundleName();
        if (!empty($bundleName)) {
            return sprintf("%s_%s",
                self::normalizeTableName(str_replace('Bundle', '', $bundleName)),
                parent::tableName()
            );
        } else {
            return parent::tableName();
        }
    }

    /**
     * Return module name
     * @return string
     */
    public static function getBundleName()
    {
        $object = new ReflectionClass(get_called_class());
        if ($pos = strpos($object->getFileName(), 'Bundle')) {
            $shortPath = substr($object->getFileName(), $pos + 7);
            return substr($shortPath, 0, strpos($shortPath, '/'));
        }
        return '';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getShortName();
    }
}