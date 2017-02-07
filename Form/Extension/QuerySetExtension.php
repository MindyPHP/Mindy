<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\FormBundle\Form\Extension;

use Mindy\Bundle\FormBundle\Form\DataTransformer\QuerySetTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class QuerySetExtension extends AbstractTypeExtension
{
    /**
     * @var QuerySetTransformer
     */
    protected $querySetTransformer;

    /**
     * QuerySetExtension constructor.
     *
     * @param QuerySetTransformer $querySetTransformer
     */
    public function __construct(QuerySetTransformer $querySetTransformer)
    {
        $this->querySetTransformer = $querySetTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer($this->querySetTransformer);
        }
    }
}
