<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 20:45
 */

namespace Mindy\Bundle\AdminBundle\Admin\Handler;

use Mindy\Orm\Manager;
use Mindy\Orm\QuerySet;
use Mindy\Orm\TreeManager;
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
    protected $defaultOrder = [];

    /**
     * OrderHandler constructor.
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
            } else {
                $method = $isTree ? 'sortNestedSet' : 'sortFlat';
                call_user_func_array([$this, $method], [$qs, $value]);
            }
        }
    }

    /**
     * @param QuerySet|Manager $qs
     * @param array $ids
     */
    public function sortFlat($qs, array $ids)
    {
        /*
         * Pager-independent sorting
         */
        $oldPositions = $qs
            ->filter(['pk__in' => $ids])
            ->valuesList([$this->column], true);
        asort($oldPositions);

        foreach ($ids as $id) {
            $qs
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
        $model = $qs->get(['pk' => $pk]);
        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }

        if ($model->getIsRoot()) {
            $roots = $qs->roots()->filter(['pk__in' => $ids])->all();
            $newPositions = array_flip($ids);

            foreach ($roots as $root) {
                $descendants = $root->objects()->descendants()->filter([
                    'level__gt' => 1,
                ])->valuesList(['pk'], true);

                if (count($descendants) > 0) {
                    $qs->filter([
                        'pk__in' => $descendants,
                    ])->update(['root' => $newPositions[$root->pk]]);
                }
            }

            foreach ($newPositions as $pk => $position) {
                $qs->filter([
                    'pk' => $pk,
                ])->update(['root' => $position]);
            }
        } else {
            if (isset($data['insertBefore'])) {
                $target = $qs->get(['pk' => $data['insertBefore']]);
                if ($target) {
                    $model->moveBefore($target);
                }
                throw new NotFoundHttpException('Target not found');
            } elseif (isset($data['insertAfter'])) {
                $target = $qs->get(['pk' => $data['insertAfter']]);
                if ($target) {
                    $model->moveAfter($target);
                }
                throw new NotFoundHttpException('Target not found');
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