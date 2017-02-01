<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin\Handler;

use Mindy\Orm\TreeManager;
use Mindy\Orm\TreeQuerySet;
use Symfony\Component\HttpFoundation\Request;

class OrderHandler implements AdminHandlerInterface
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $defaultOrder = [];

    /**
     * OrderHandler constructor.
     *
     * @param Request $request
     * @param $name
     * @param array $defaultOrder
     */
    public function __construct(Request $request, $name, array $defaultOrder = null)
    {
        $this->request = $request;
        $this->name = $name;
        $this->defaultOrder = $defaultOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($qs)
    {
        $value = $this->getValue();
        $isTree = $qs instanceof TreeManager || $qs instanceof TreeQuerySet;

        if (empty($value)) {
            if (false == empty($this->defaultOrder)) {
                if ($isTree && is_array($this->defaultOrder)) {
                    $columns = array_merge(['root', 'lft'], $this->defaultOrder);
                } else {
                    $columns = [$this->defaultOrder];
                }

                $qs->order($columns);
            } elseif ($isTree) {
                $qs->order(['root', 'lft']);
            } else {
                return;
            }
        } else {
            if (!is_array($value)) {
                $value = [$value];
            }
            $qs->order($value);
        }
    }

    /**
     * @param $column
     *
     * @return string
     */
    public function generateUrl($column)
    {
        $order = $this->getValue();
        if ($order == $column) {
            $column = '-'.$column;
        }
        $queryString = http_build_query(array_merge($this->request->query->all(), [$this->name => $column]));

        return strtok($this->request->getUri(), '?').'?'.$queryString;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->request->query->get($this->name);
    }
}
