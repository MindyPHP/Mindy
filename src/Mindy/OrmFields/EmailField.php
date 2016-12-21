<?php

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
