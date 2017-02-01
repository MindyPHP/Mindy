<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\TableBundle\Library;

use Mindy\Component\Table\TableView;
use Mindy\Template\Library;
use Mindy\Template\Renderer;

class TableLibrary extends Library
{
    protected $template;

    public function __construct(Renderer $template)
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'table_render' => function (TableView $table, $template = 'table/table.html') {
                return $this->template->render($template, $table->getData());
            },
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
