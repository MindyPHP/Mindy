<?php
/**
 * Created by IntelliJ IDEA.
 * User: max
 * Date: 14/03/2017
 * Time: 20:16
 */

namespace Mindy\Orm\FileNameHasher;

class MD5NameHasher extends DefaultHasher
{
    /**
     * {@inheritdoc}
     */
    public function hash($fileName)
    {
        return md5($fileName);
    }
}
