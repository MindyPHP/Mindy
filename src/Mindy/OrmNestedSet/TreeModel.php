<?php

/*
 * This file is part of Mindy Orm.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm;

use Exception;
use Mindy\Orm\Fields\ForeignField;
use Mindy\Orm\Fields\IntField;
use Mindy\QueryBuilder\Expression;

/**
 * Class TreeModel.
 *
 * @method static \Mindy\Orm\TreeManager objects($instance = null)
 *
 * @property TreeModel|null $parent
 * @property int|null $parent_id
 * @property int $root
 * @property int $level
 * @property int $lft
 * @property int $rgt
 */
abstract class TreeModel extends Model
{
    public static function getFields()
    {
        return [
            'parent' => [
                'class' => ForeignField::class,
                'modelClass' => get_called_class(),
                'null' => true,
                'verboseName' => 'Родитель',
            ],
            'lft' => [
                'class' => IntField::class,
                'editable' => false,
                'null' => true,
            ],
            'rgt' => [
                'class' => IntField::class,
                'editable' => false,
                'null' => true,
            ],
            'level' => [
                'class' => IntField::class,
                'editable' => false,
                'null' => true,
            ],
            'root' => [
                'class' => IntField::class,
                'editable' => false,
                'null' => true,
            ],
        ];
    }

    /**
     * @param array $item
     * @return array
     */
    public function toTree(array $item)
    {
        return $item;
    }

    /**
     * @param null $instance
     *
     * @return TreeManager
     */
    public static function objectsManager($instance = null)
    {
        if (!$instance) {
            $className = get_called_class();
            $instance = new $className();
        }

        if (class_exists($managerClass = self::getManagerClass())) {
            return new $managerClass($instance, $instance->getConnection());
        }

        return new TreeManager($instance, $instance->getConnection());
    }

    /**
     * @DEPRECATED
     *
     * @param null $instance
     *
     * @return TreeManager
     */
    public static function treeManager($instance = null)
    {
        return self::objectsManager($instance);
    }

    /**
     * Determines if node is leaf.
     *
     * @return bool whether the node is leaf
     */
    public function getIsLeaf()
    {
        return $this->rgt - $this->lft === 1;
    }

    /**
     * Determines if node is root.
     *
     * @return bool whether the node is root
     */
    public function getIsRoot()
    {
        return $this->lft == 1;
    }

    /**
     * @var bool
     */
    private $_deleted = false;

    /**
     * Create root node if multiple-root tree mode. Update node if it's not new.
     *
     * @param array $fields
     *
     * @throws \Exception
     *
     * @return bool whether the saving succeeds
     */
    public function save(array $fields = [])
    {
        if ($this->getIsNewRecord()) {
            if ($this->parent) {
                $this->appendTo($this->parent);
            } else {
                $this->makeRoot();
            }

            return parent::save($fields);
        }
        if (in_array('parent_id', $this->getDirtyAttributes())) {
            if ($saved = parent::save($fields)) {
                if ($this->parent) {
                    $this->moveAsLast($this->parent);
                } elseif ($this->isRoot() == false) {
                    $this->moveAsRoot();
                }
                    /** @var array $parent */
                    $parent = $this->objects()->asArray()->get(['pk' => $this->pk]);
                if ($parent !== null) {
                    $this->setAttributes($parent);
                    $this->setIsNewRecord(false);
                }

                return $saved;
            }

            return $saved;
        }

        return parent::save($fields);
    }

    public function saveRebuild()
    {
        $fields = ['lft', 'rgt', 'level', 'root'];

        if ($this->parent == null) {
            $this->lft = 1;
            $this->rgt = 2;
            $this->level = 0;
            $this->root = $this->pk;

            return parent::save($fields);
        } elseif ($this->parent->lft) {
            $target = $this->parent;
            $key = $target->rgt;

            $this->root = $target->root;

            $this->shiftLeftRight($key, 2);
            $this->lft = $key;
            $this->rgt = $key + 1;
            $this->level = $target->level + 1;

            return parent::save($fields);
        }

        return false;
    }

    /**
     * Deletes node and it's descendants.
     *
     * @throws \Exception
     *
     * @return bool whether the deletion is successful
     */
    public function delete()
    {
        if ($this->isLeaf()) {
            $result = parent::delete();
        } else {
            $result = $this->objects()->filter([
                'lft__gte' => $this->lft,
                'rgt__lte' => $this->rgt,
                'root' => $this->root,
            ])->delete();
        }

        return (bool) $result;
    }

    /**
     * Prepends node to target as first child.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the prepending succeeds
     */
    public function prependTo(TreeModel $target)
    {
        return $this->addNode($target, $target->lft + 1, 1);
    }

    /**
     * Prepends target to node as first child.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the prepending succeeds
     */
    public function prepend(TreeModel $target)
    {
        return $target->prependTo($this);
    }

    /**
     * Appends node to target as last child.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the appending succeeds
     */
    public function appendTo(TreeModel $target)
    {
        return $this->addNode($target, (int) $target->rgt, 1);
    }

    /**
     * Appends target to node as last child.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the appending succeeds
     */
    public function append(TreeModel $target)
    {
        return $target->appendTo($this);
    }

    /**
     * Inserts node as previous sibling of target.
     *
     * @param TreeModel $target the target
     *
     * @throws \Exception
     *
     * @return bool whether the inserting succeeds
     */
    public function insertBefore(TreeModel $target)
    {
        return $this->addNode($target, $target->lft, 0);
    }

    /**
     * Inserts node as next sibling of target.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the inserting succeeds
     */
    public function insertAfter(TreeModel $target)
    {
        return $this->addNode($target, $target->rgt + 1, 0);
    }

    /**
     * Move node as previous sibling of target.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the moving succeeds
     */
    public function moveBefore(TreeModel $target)
    {
        return $this->moveNode($target, $target->lft, 0);
    }

    /**
     * Move node as next sibling of target.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the moving succeeds
     */
    public function moveAfter(TreeModel $target)
    {
        return $this->moveNode($target, $target->rgt + 1, 0);
    }

    /**
     * Move node as first child of target.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the moving succeeds
     */
    public function moveAsFirst(TreeModel $target)
    {
        return $this->moveNode($target, $target->lft + 1, 1);
    }

    /**
     * Move node as last child of target.
     *
     * @param TreeModel $target the target
     *
     * @return bool whether the moving succeeds
     */
    public function moveAsLast(TreeModel $target)
    {
        return $this->moveNode($target, $target->rgt, 1);
    }

    /**
     * Move node as new root.
     *
     * @throws Exception
     * @throws \Exception
     *
     * @return bool whether the moving succeeds
     */
    public function moveAsRoot()
    {
        if ($this->getIsNewRecord()) {
            throw new Exception('The node should not be new record.');
        }

        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node should not be deleted.');
        }

        if ($this->isRoot()) {
            throw new Exception('The node already is root node.');
        }

        $left = $this->lft;
        $right = $this->rgt;
        $levelDelta = 1 - $this->level;
        $delta = 1 - $left;
        $this->objects()
            ->filter([
                'lft__gte' => $left,
                'rgt__lte' => $right,
                'root' => $this->root,
            ])
            ->update([
                'lft' => new Expression('lft'.sprintf('%+d', $delta)),
                'rgt' => new Expression('rgt'.sprintf('%+d', $delta)),
                'level' => new Expression('level'.sprintf('%+d', $levelDelta)),
                'root' => $this->getMaxRoot(),
            ]);
        $this->shiftLeftRight($right + 1, $left - $right - 1);

        return true;
    }

    /**
     * Determines if node is descendant of subject node.
     *
     * @param TreeModel $subj the subject node
     *
     * @return bool whether the node is descendant of subject node
     */
    public function isDescendantOf($subj)
    {
        return ($this->lft > $subj->lft) && ($this->rgt < $subj->rgt) && ($this->root === $subj->root);
    }

    /**
     * Determines if node is leaf.
     *
     * @return bool whether the node is leaf
     */
    public function isLeaf()
    {
        return $this->rgt - $this->lft === 1;
    }

    /**
     * Determines if node is root.
     *
     * @return bool whether the node is root
     */
    public function isRoot()
    {
        return $this->getIsRoot();
    }

    /**
     * Returns if the current node is deleted.
     *
     * @return bool whether the node is deleted
     */
    public function getIsDeletedRecord()
    {
        return $this->_deleted;
    }

    /**
     * Sets if the current node is deleted.
     *
     * @param bool $value whether the node is deleted
     */
    public function setIsDeletedRecord($value)
    {
        $this->_deleted = $value;
    }

    /**
     * @param int $value
     * @param int $delta
     */
    private function shiftLeftRight($value, $delta)
    {
        $conditions = ['root' => $this->root];

        foreach (['lft', 'rgt'] as $attribute) {
            $qs = $this->objects()->filter(array_merge($conditions, [
                $attribute.'__gte' => $value,
            ]));

            $qs->update([
                $attribute => new Expression('[['.$attribute.']]'.sprintf('%+d', $delta)),
            ]);
        }
    }

    /**
     * @param TreeModel $target
     * @param int       $rgt
     * @param int       $levelUp
     *
     * @throws \Exception
     *
     * @return $this
     */
    private function addNode(TreeModel $target, $rgt, $levelUp)
    {
        if (!$this->getIsNewRecord()) {
            throw new Exception("The node can't be inserted because it is not new.");
        }

        if ($this->getIsDeletedRecord()) {
            throw new Exception("The node can't be inserted because it is deleted.");
        }

        if ($target->getIsDeletedRecord()) {
            throw new Exception("The node can't be inserted because target node is deleted.");
        }

        if ($this->pk == $target->pk) {
            throw new Exception('The target node should not be self.');
        }

        if (!$levelUp && $target->isRoot()) {
            throw new Exception('The target node should not be root.');
        }

        $this->root = $target->root;

        $this->shiftLeftRight($rgt, 2);

        $this->setAttributes([
            'lft' => $rgt,
            'rgt' => $rgt + 1,
            'level' => $target->level + $levelUp,
        ]);

        return $this;
    }

    private function getMaxRoot()
    {
        return $this->objects()->max('root') + 1;
    }

    /**
     * @throws \Exception
     *
     * @return $this
     */
    private function makeRoot()
    {
        $this->lft = 1;
        $this->rgt = 2;
        $this->level = 1;
        $this->root = $this->getMaxRoot();

        return $this;
    }

    /**
     * @param TreeModel $target
     * @param int       $key
     * @param int       $levelUp
     *
     * @throws Exception
     * @throws \Exception
     *
     * @return bool
     */
    private function moveNode(TreeModel $target, $key, $levelUp)
    {
        if ($this->getIsNewRecord()) {
            throw new Exception('The node should not be new record.');
        }

        if ($this->getIsDeletedRecord()) {
            throw new Exception('The node should not be deleted.');
        }

        if ($target->getIsDeletedRecord()) {
            throw new Exception('The target node should not be deleted.');
        }

        if ($this->pk == $target->pk) {
            throw new Exception('The target node should not be self.');
        }

        if ($target->isDescendantOf($this)) {
            throw new Exception('The target node should not be descendant.');
        }

        if (!$levelUp && $target->isRoot()) {
            throw new Exception('The target node should not be root.');
        }

        $left = $this->lft;
        $right = $this->rgt;
        $levelDelta = $target->level - $this->level + $levelUp;

        if ($this->root !== $target->root) {
            foreach (['lft', 'rgt'] as $attribute) {
                $this->objects()
                    ->filter([$attribute.'__gte' => $key, 'root' => $target->root])
                    ->update([$attribute => new Expression($attribute.sprintf('%+d', $right - $left + 1))]);
            }

            $delta = $key - $left;
            $this->objects()
                ->filter(['lft__gte' => $left, 'rgt__lte' => $right, 'root' => $this->root])
                ->update([
                    'lft' => new Expression('lft'.sprintf('%+d', $delta)),
                    'rgt' => new Expression('rgt'.sprintf('%+d', $delta)),
                    'level' => new Expression('level'.sprintf('%+d', $levelDelta)),
                    'root' => $target->root,
                ]);

            $this->shiftLeftRight($right + 1, $left - $right - 1);
        } else {
            $delta = $right - $left + 1;
            $this->shiftLeftRight($key, $delta);

            if ($left >= $key) {
                $left += $delta;
                $right += $delta;
            }

            $this->objects()
                ->filter(['lft__gte' => $left, 'rgt__lte' => $right, 'root' => $this->root])
                ->update([
                    'level' => new Expression('level'.sprintf('%+d', $levelDelta)),
                ]);

            foreach (['lft', 'rgt'] as $attribute) {
                $this->objects()
                    ->filter([$attribute.'__gte' => $left, $attribute.'__lte' => $right, 'root' => $this->root])
                    ->update([$attribute => new Expression($attribute.sprintf('%+d', $key - $left))]);
            }

            $this->shiftLeftRight($right + 1, -$delta);
        }

        return true;
    }
}
