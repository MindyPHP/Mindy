<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
