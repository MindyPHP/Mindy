<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
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
