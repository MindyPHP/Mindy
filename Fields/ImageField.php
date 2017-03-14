<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Orm\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ImageField.
 */
class ImageField extends FileField
{
    /**
     * @var array
     */
    public $mimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    /**
     * @var Assert\Image validation settings
     */
    public $minWidth;
    public $maxWidth;
    public $maxHeight;
    public $minHeight;
    public $maxRatio;
    public $minRatio;
    public $allowSquare = true;
    public $allowLandscape = true;
    public $allowPortrait = true;
    public $detectCorrupted = false;

    /**
     * @return array
     */
    public function getValidationConstraints()
    {
        $constraints = parent::getValidationConstraints();

        if ($this->isRequired() && empty($this->value)) {
            return array_merge($constraints, [
                new Assert\Image([
                    'minWidth' => $this->minWidth,
                    'maxWidth' => $this->maxWidth,
                    'maxHeight' => $this->maxHeight,
                    'minHeight' => $this->minHeight,
                    'maxRatio' => $this->maxRatio,
                    'minRatio' => $this->minRatio,
                    'allowSquare' => $this->allowSquare,
                    'allowLandscape' => $this->allowLandscape,
                    'allowPortrait' => $this->allowPortrait,
                    'detectCorrupted' => $this->detectCorrupted,
                ]),
            ]);
        }

        return $constraints;
    }
}
