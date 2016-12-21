<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 27/11/2016
 * Time: 18:03.
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
