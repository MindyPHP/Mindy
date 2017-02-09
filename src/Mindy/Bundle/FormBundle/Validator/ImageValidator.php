<?php
/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\FormBundle\Validator;

use Mindy\Orm\Fields\FileField;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator as BaseImageValidator;

class ImageValidator extends BaseImageValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === FileField::IGNORE) {
            return;
        }

        parent::validate($value, $constraint);
    }
}