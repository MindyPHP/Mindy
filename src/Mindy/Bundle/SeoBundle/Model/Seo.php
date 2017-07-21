<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SeoBundle\Model;

use Mindy\Orm\Fields\BooleanField;
use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\Model;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Meta.
 *
 * @property string $host
 * @property string $title
 * @property string $url
 * @property string $keywords
 * @property string $canonical
 * @property string $description
 * @property int|bool $is_auto
 */
class Seo extends Model
{
    public static function getFields()
    {
        return [
            'host' => [
                'class' => CharField::class,
            ],
            'title' => [
                'class' => CharField::class,
                'length' => 60,
                'null' => true,
            ],
            'url' => [
                'class' => CharField::class,
                'unique' => true,
                'validators' => [
                    new Assert\Url(),
                ],
            ],
            'keywords' => [
                'class' => CharField::class,
                'length' => 60,
                'null' => true,
            ],
            'canonical' => [
                'class' => CharField::class,
                'unique' => true,
                'null' => true,
                'validators' => [
                    new Assert\Url(),
                ],
            ],
            'description' => [
                'class' => TextField::class,
                'length' => 160,
                'null' => true,
            ],
            'is_auto' => [
                'class' => BooleanField::class,
                'default' => true,
            ],
        ];
    }

    public function __toString()
    {
        return sprintf('%s/%s', $this->host, $this->url);
    }
}
