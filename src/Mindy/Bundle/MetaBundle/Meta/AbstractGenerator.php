<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\MetaBundle\Meta;

use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractGenerator implements MetaGeneratorInterface
{
    /**
     * @var MetaSourceInterface
     */
    protected $source;
    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * Generator constructor.
     *
     * @param MetaSourceInterface $source
     * @param array $parameters
     */
    public function __construct(MetaSourceInterface $source, array $parameters = [])
    {
        $this->propertyAccessor = new PropertyAccessor();

        $this->source = $source;

        foreach ($parameters as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
