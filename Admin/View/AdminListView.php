<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 19:39
 */

namespace Mindy\Bundle\AdminBundle\View\Admin;

use Mindy\Bundle\AdminBundle\Admin\Handler\AdminHandlerInterface;
use Mindy\Bundle\AdminBundle\View\ListView;
use Symfony\Component\HttpFoundation\Request;

class AdminListView extends ListView
{
    /**
     * @var AdminHandlerInterface
     */
    protected $searchHandler;
    /**
     * @var AdminHandlerInterface
     */
    protected $orderHandler;
    /**
     * @var AdminHandlerInterface
     */
    protected $sortHandler;

    /**
     * @param AdminHandlerInterface $searchHandler
     */
    public function setSearchHandler(AdminHandlerInterface $searchHandler)
    {
        $this->searchHandler = $searchHandler;
    }

    /**
     * @param AdminHandlerInterface $orderHandler
     */
    public function setOrderHandler(AdminHandlerInterface $orderHandler)
    {
        $this->orderHandler = $orderHandler;
    }

    /**
     * @param AdminHandlerInterface $sortHandler
     */
    public function setSortHandler(AdminHandlerInterface $sortHandler)
    {
        $this->sortHandler = $sortHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(Request $request)
    {
        $qs = $this->getQuerySet();

        if ($this->searchHandler) {
            $this->searchHandler->handle($qs);
        }

        if ($this->orderHandler) {
            $this->orderHandler->handle($qs);
        }

        if ($this->sortHandler) {
            $this->sortHandler->handle($qs);
        }
    }

    public function getContextData()
    {
        return array_merge(parent::getContextData(), [
            'orderHandler' => $this->orderHandler,
            'sortHandler' => $this->sortHandler,
            'searchHandler' => $this->searchHandler,
        ]);
    }
}