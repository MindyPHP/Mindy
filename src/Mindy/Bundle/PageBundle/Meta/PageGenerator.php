<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Meta;

use Mindy\Bundle\MetaBundle\Meta\MetaGeneratorInterface;
use Mindy\Bundle\MetaBundle\Meta\MetaSourceInterface;
use Mindy\Bundle\PageBundle\Model\Page;

class PageGenerator implements MetaGeneratorInterface
{
    /**
     * @param $object
     *
     * @return bool
     */
    public function support($object)
    {
        return $object instanceof Page;
    }

    /**
     * @param MetaSourceInterface $source
     *
     * @return array
     */
    public function build(MetaSourceInterface $source)
    {
        return [
            'title' => 'pizda',
        ];
    }
}
