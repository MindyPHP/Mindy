<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin\Handler;

use Mindy\Orm\Fields\CharField;
use Mindy\Orm\Fields\TextField;
use Mindy\Orm\Manager;
use Mindy\Orm\QuerySet;
use Mindy\QueryBuilder\Q\QOr;
use Symfony\Component\HttpFoundation\Request;

class SearchHandler implements AdminHandlerInterface
{
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * SearchHandler constructor.
     *
     * @param Request $request
     * @param string $name
     * @param array $fields
     */
    public function __construct(Request $request, $name, array $fields = null)
    {
        $this->request = $request;
        $this->name = $name;
        $this->fields = $fields;
    }

    /**
     * @param QuerySet|Manager $qs
     *
     * @return array
     */
    protected function getFields($qs)
    {
        $fields = [];
        if (null === $this->fields) {
            $modelFields = $qs->getModel()->getMeta()->getFields();
            $allowed = [
                CharField::class,
                TextField::class,
            ];

            foreach ($modelFields as $name => $field) {
                if (in_array(get_class($field), $allowed)) {
                    $fields[] = $name;
                }
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($qs)
    {
        $value = $this->getValue();
        $fields = $this->getFields($qs);
        if (empty($value) || empty($fields)) {
            return;
        }

        $filters = [];
        foreach ($fields as $key => $temp) {
            if (is_numeric($key)) {
                $field = $temp;
                $lookup = 'icontains';
            } else {
                $field = $key;
                $lookup = $temp;
            }

            $filters[] = [$field.'__'.$lookup => $value];
        }

        $qs->filter([
            new QOr($filters),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->request->query->get($this->name);
    }
}
