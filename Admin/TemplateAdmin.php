<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SeoBundle\Admin;

use Mindy\Bundle\AdminBundle\Admin\AbstractModelAdmin;
use Mindy\Bundle\SeoBundle\Form\TemplateForm;
use Mindy\Bundle\SeoBundle\Model\Template;

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
