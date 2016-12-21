<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 06/12/2016
 * Time: 22:31.
 */

namespace Mindy\Orm\Fields;

abstract class AbstractSlugField extends CharField
{
    use SlugifyTrait;

    /**
     * @var string
     */
    public $source = 'name';
}
