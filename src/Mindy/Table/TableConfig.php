<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 23:53.
 */

namespace Mindy\Component\Table;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TableConfig
{
    protected $optionsResolver;

    public function __construct()
    {
        $this->optionsResolver = new OptionsResolver();
    }

    public function getOptionsResolver()
    {
        return $this->optionsResolver;
    }
}
