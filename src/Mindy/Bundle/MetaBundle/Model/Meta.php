<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\MetaBundle\Model;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\Model;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Meta.
 *
 * @property string $domain
 * @property string $title
 * @property string $url
 * @property string $keywords
 * @property string $canonical
 * @property string $description
 */
class Meta extends Model
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
            ],
            'url' => [
                'class' => CharField::class,
            ],
            'keywords' => [
                'class' => CharField::class,
                'length' => 60,
            ],
            'canonical' => [
                'class' => CharField::class,
                'validators' => [
                    new Assert\Url(),
                ],
            ],
            'description' => [
                'class' => TextField::class,
                'length' => 160,
            ],
        ];
    }

    public function __toString()
    {
        return sprintf('%s/%s', $this->host, $this->url);
    }
}
