<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\Normalizer;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class FormErrorsNormalizer extends SerializerAwareNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($object instanceof FormErrorIterator) {
            return $this->iterateFormErrors($object);
        }

        return $this->iterateFormErrors($object->getErrors(true, false));
    }

    protected function iterateFormErrors($iterator)
    {
        $errors = [];
        foreach ($iterator as $error) {
            if ($error instanceof FormError) {
                $errors[] = $error->getMessage();
            } else {
                /* @var $error FormErrorIterator */
                $errors[$error->getForm()->getName()] = $this->iterateFormErrors($error);
            }
        }

        return $errors;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof FormInterface || $data instanceof FormErrorIterator;
    }
}
