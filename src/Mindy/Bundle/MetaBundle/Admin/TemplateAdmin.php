<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MetaBundle\Admin;

use Mindy\Bundle\AdminBundle\Admin\AbstractModelAdmin;
use Mindy\Bundle\MetaBundle\Form\TemplateForm;
use Mindy\Bundle\MetaBundle\Model\Template;

class TemplateAdmin extends AbstractModelAdmin
{
    public $columns = ['code'];

    public function getFormType()
    {
        return TemplateForm::class;
    }

    public function getModelClass()
    {
        return Template::class;
    }
}
