<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SeoBundle\Meta;

class Registry
{
    /**
     * @var MetaGeneratorInterface[]
     */
    protected $generators = [];

    /**
     * @param MetaGeneratorInterface $generator
     */
    public function addGenerator(MetaGeneratorInterface $generator)
    {
        $this->generators[] = $generator;
    }

    /**
     * @param MetaSourceInterface $metaSource
     *
     * @return array|null
     */
    public function build(MetaSourceInterface $metaSource)
    {
        foreach ($this->generators as $generator) {
            if ($generator->support($metaSource)) {
                return $generator->build($metaSource);
            }
        }

        return null;
    }
}
