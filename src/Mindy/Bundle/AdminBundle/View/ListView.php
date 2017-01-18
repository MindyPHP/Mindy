<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 16:44.
 */

namespace Mindy\Bundle\AdminBundle\View;

use Mindy\Bundle\AdminBundle\Form\FilterFormInterface;
use Mindy\Orm\Manager;
use Mindy\Orm\QuerySet;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ListView extends AbstractFormView
{
    /**
     * @var QuerySet|Manager
     */
    protected $querySet;
    /**
     * @var array
     */
    protected $paginationParameters = array();
    /**
     * @var FilterFormInterface|FormInterface
     */
    protected $filterForm;
    /**
     * @param $qs
     */
    public function setQuerySet($qs)
    {
        $this->querySet = $qs;
    }

    /**
     * @return Manager|QuerySet
     */
    public function getQuerySet()
    {
        return $this->querySet;
    }

    /**
     * @param FilterFormInterface $filterForm
     */
    public function setFilterForm(FilterFormInterface $filterForm)
    {
        $this->filterForm = $filterForm;
    }

    /**
     * @return FilterFormInterface
     */
    public function getFilterForm()
    {
        return $this->filterForm;
    }

    /**
     * @param Request $request
     */
    public function handleRequest(Request $request)
    {
        $qs = $this->getQuerySet();

        if ($filterForm = $this->getFilterForm()) {
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                $filterForm->filter($qs);
            }
        }
    }

    /**
     * @param array $parameters
     */
    public function setPaginationParameters(array $parameters = array())
    {
        $this->paginationParameters = $parameters;
    }

    /**
     * @return array
     */
    public function getPaginationParameters()
    {
        return $this->paginationParameters;
    }

    /**
     * @return array
     */
    public function getContextData()
    {
        $qs = $this->getQuerySet();
        $filterForm = $this->getFilterForm();

        $data = [
            'filter_form' => $filterForm ? $filterForm->createView() : null,
        ];
        if ($this->container->has('pagination.factory')) {
            $pager = $this->container->get('pagination.factory')->createPagination(
                $qs,
                $this->getPaginationParameters(),
                $this->container->get('pagination.handler')
            );

            $data = array_merge($data, [
                'models' => $pager->paginate(),
                'pager' => $pager->createView(),
            ]);
        } else {
            $data = array_merge($data, [
                'models' => $qs->all(),
                'pager' => false,
            ]);
        }

        return $data;
    }
}
