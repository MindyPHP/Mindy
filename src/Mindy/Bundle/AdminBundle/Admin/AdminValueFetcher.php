<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin;

use Mindy\Orm\Fields\BooleanField;
use Mindy\Orm\Fields\HasManyField;
use Mindy\Orm\Fields\ManyToManyField;
use Mindy\Orm\ModelInterface;

class AdminValueFetcher
{
    /**
     * @param $column
     * @param $model
     *
     * @return array
     */
    public function getChainedModel($column, $model)
    {
        if (strpos($column, '__') !== false) {
            $exploded = explode('__', $column);
            $last = count($exploded) - 1;
            $column = null;
            foreach ($exploded as $key => $name) {
                if ($model instanceof ModelInterface) {
                    $value = $model->{$name};
                    $column = $name;
                    if ($key != $last && $value) {
                        $model = $value;
                    }
                } else {
                    $model = null;
                    break;
                }
            }
        }

        return [$column, $model];
    }

    /**
     * @param $column
     * @param ModelInterface|\Mindy\Orm\Model $model
     *
     * @return mixed
     */
    public function fetchValue($column, ModelInterface $model)
    {
        list($column, $model) = $this->getChainedModel($column, $model);
        if ($model === null) {
            return;
        }

        $column = $model->convertToPrimaryKeyName($column);
        $booleanHtml = '<i class="icon checkmark" aria-hidden="true"></i>';
        if ($model->hasField($column)) {
            $field = $model->getField($column);
            if ($field instanceof ManyToManyField || $field instanceof HasManyField) {
                return get_class($model->{$column});
            }
            $value = $model->{$column};

            if ($model->getField($column) instanceof BooleanField) {
                return $value ? $booleanHtml : '';
            }

            return $value;
        }
        $method = 'get'.ucfirst($column);
        if (method_exists($model, $method)) {
            return $model->{$method}();
        }
    }
}
