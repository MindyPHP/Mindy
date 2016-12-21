<?php

namespace Mindy\Bundle\MenuBundle\Model;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\TreeModel;
use Mindy\Validation\Alphanumeric;

/**
 * Class Menu.
 *
 * @property string $slug
 * @property string $name
 * @property string $url
 */
class Menu extends TreeModel
{
    public static function getFields()
    {
        return array_merge(parent::getFields(), [
            'slug' => [
                'class' => CharField::class,
                'null' => true,
                'validators' => [
                    new Alphanumeric(),
                ],
                'helpText' => 'Ключ для выбора меню. Может содержать только латинские символы и цифры.',
            ],
            'name' => [
                'class' => CharField::class,
            ],
            'url' => [
                'class' => CharField::class,
                'null' => true,
                'default' => '#',
                'helpText' => 'Ссылка может быть абсолютной, относительной или любым js кодом',
            ],
        ]);
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
