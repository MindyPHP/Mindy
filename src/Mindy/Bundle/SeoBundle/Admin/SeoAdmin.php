<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\SeoBundle\Admin;

use Mindy\Bundle\AdminBundle\Admin\AbstractModelAdmin;
use Mindy\Bundle\SeoBundle\Form\SeoFilterForm;
use Mindy\Bundle\SeoBundle\Form\SeoFormType;
use Mindy\Bundle\SeoBundle\Model\Seo;

class SeoAdmin extends AbstractModelAdmin
{
    public $columns = ['host', 'url', 'title'];

    public function getFilterFormType()
    {
        return SeoFilterForm::class;
    }

    /**
     * @return string model class name
     */
    public function getModelClass()
    {
        return Seo::class;
    }

    public function getFormType()
    {
        return SeoFormType::class;
    }
}
