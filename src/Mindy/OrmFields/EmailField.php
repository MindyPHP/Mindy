<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Orm\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EmailField.
 */
class EmailField extends CharField
{
    /**
     * @var bool
     */
    public $checkMX = false;
    /**
     * @var bool
     */
    public $checkHost = false;

    /**
     * @return array
     */
    public function getValidationConstraints()
    {
        return array_merge(parent::getValidationConstraints(), [
            new Assert\Email([
                'checkMX' => $this->checkMX,
                'checkHost' => $this->checkHost,
            ]),
        ]);
    }
}
