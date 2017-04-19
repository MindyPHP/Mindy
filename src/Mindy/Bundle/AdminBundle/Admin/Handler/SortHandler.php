<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin\Handler;

use Mindy\Orm\Manager;
use Mindy\Orm\QuerySet;
use Mindy\Orm\TreeManager;
use Mindy\Orm\TreeModel;
use Mindy\Orm\TreeQuerySet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SortHandler implements AdminHandlerInterface
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
    protected $column;
    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $defaultOrder = [];

    /**
     * OrderHandler constructor.
     *
     * @param Request $request
     * @param string $name
     * @param string $column
     * @param null $field
     */
    public function __construct(Request $request, $name, $column, $field = null)
    {
        $this->request = $request;
        $this->name = $name;
        $this->column = $column;
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($qs)
    {
        $isTree = $qs instanceof TreeManager || $qs instanceof TreeQuerySet;

        if ($this->request->getMethod() === 'POST') {
            $value = $this->getValue();
            if (empty($value)) {
                if ($isTree) {
                    $qs->order(['root', 'lft']);
                } else {
                    $qs->order([$this->column]);
                }

                return;
            }

            $method = $isTree ? 'sortNestedSet' : 'sortFlat';
            call_user_func_array([$this, $method], [$qs, $value]);
        }
    }

    /**
     * @param QuerySet|Manager $qs
     * @param array $ids
     */
    public function sortFlat($qs, array $ids)
    {
        $cloneQs = clone $qs;

        /*
         * Pager-independent sorting
         */
        $oldPositions = $cloneQs
            ->filter(['pk__in' => $ids])
            ->valuesList([$this->column], true);
        asort($oldPositions);

        foreach ($ids as $id) {
            (clone $qs)
                ->filter(['pk' => $id])
                ->update([
                    $this->column => array_shift($oldPositions),
                ]);
        }
    }

    /**
     * @param TreeQuerySet|TreeManager $qs
     * @param array $ids
     */
    public function sortNestedSet($qs, array $ids)
    {
        if (false == $this->request->query->has('pk')) {
            throw new NotFoundHttpException('Failed to receive primary key');
        }

        $pk = $this->request->query->getInt('pk');

        /** @var \Mindy\Orm\TreeModel $model */
        $model = $qs->getModel()->objects()->get(['pk' => $pk]);
        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }

        if ($model->getIsRoot()) {
            $roots = $model
                ->objects()
                ->roots()
                ->filter(['pk__in' => $ids])
                ->all();

            $newPositions = array_flip($ids);

            foreach ($roots as $root) {
                /** @var TreeModel $root */
                $descendantIds = $root
                    ->objects()
                    ->descendants()
                    ->filter(['level__gt' => 1])
                    ->valuesList(['pk'], true);

                if (count($descendantIds) > 0) {
                    $model
                        ->objects()
                        ->filter(['pk__in' => $descendantIds])
                        ->update(['root' => $newPositions[$root->pk]]);
                }
            }

            foreach ($newPositions as $pk => $position) {
                $model
                    ->objects()
                    ->filter(['pk' => $pk])
                    ->update(['root' => $position]);
            }
        } else {
            /** @var TreeModel $target */
            if ($this->request->query->has('insertBefore')) {
                $target = $model->objects()->get([
                    'pk' => $this->request->query->get('insertBefore')
                ]);
                if (null === $target) {
                    throw new NotFoundHttpException('Target not found');
                }

                $model->moveBefore($target);
            } elseif ($this->request->query->has('insertAfter')) {
                $target = $model->objects()->get([
                    'pk' => $this->request->query->get('insertAfter')
                ]);
                if (null === $target) {
                    throw new NotFoundHttpException('Target not found');
                }

                $model->moveAfter($target);
            } else {
                throw new NotFoundHttpException('Missing required parameter insertAfter or insertBefore');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->request->query->get($this->name);
    }
}
