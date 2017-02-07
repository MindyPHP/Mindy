<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/02/2017
 * Time: 20:52
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
     * @return array
     */
    public function build(MetaSourceInterface $source)
    {
        return [
            'title' => 'pizda'
        ];
    }
}