<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MindyBundle\Library;

use Mindy\Template\Library;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper;

class FormLibrary extends Library
{
    protected $formHelper;

    public function __construct(FormHelper $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'form_start' => [$this->formHelper, 'start'],
            'form_end' => [$this->formHelper, 'end'],
            'form_block' => [$this->formHelper, 'block'],
            'form_render' => [$this->formHelper, 'form'],
            'form_label' => [$this->formHelper, 'label'],
            'form_errors' => [$this->formHelper, 'errors'],
            'form_row' => [$this->formHelper, 'row'],
            'form_rest' => [$this->formHelper, 'rest'],
            'form_widget' => [$this->formHelper, 'widget'],
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
