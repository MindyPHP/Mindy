<?php

namespace Mindy\Orm\Fields;

use Mindy\Orm\ModelInterface;

/**
 * Class SlugField.
 */
class SlugField extends AbstractSlugField
{
    public function beforeInsert(ModelInterface $model, $value)
    {
        $this->value = empty($this->value) ? $this->createSlug($model->{$this->source}) : $this->value;
        if ($this->unique) {
            $this->value = $this->uniqueUrl($this->value);
        }
        $model->setAttribute($this->getAttributeName(), $this->value);
    }

    public function canBeEmpty()
    {
        return true;
    }

    public function beforeUpdate(ModelInterface $model, $value)
    {
        $this->value = $value;

        // Случай когда обнулен slug, например из админки
        if (empty($value)) {
            $this->value = $this->createSlug($model->{$this->source});
        }

        if ($this->unique) {
            $this->value = $this->uniqueUrl($this->value, 0, $model->pk);
        }
        $model->setAttribute($this->getAttributeName(), $this->value);
    }
}
