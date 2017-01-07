<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 13/11/2016
 * Time: 20:53.
 */

namespace Mindy\Bundle\AdminBundle\Admin;

use Mindy\Bundle\MindyBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class AbstractAdmin extends Controller implements AdminInterface
{
    const FLASH_SUCCESS = 'admin_success';
    const FLASH_NOTICE = 'admin_notice';
    const FLASH_WARNING = 'admin_warning';
    const FLASH_ERROR = 'admin_error';

    /**
     * @var string
     */
    protected $adminId;
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        if (null === $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher();
        }
        return $this->eventDispatcher;
    }

    /**
     * @return string
     */
    public function classNameShort()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function setAdminId($id)
    {
        $this->adminId = $id;
    }

    protected function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param $action
     * @param array $params
     *
     * @return string
     */
    public function getAdminUrl($action, array $params = [])
    {
        return $this->generateUrl('admin_dispatch', array_merge($params, [
            'admin' => $this->getAdminId(),
            'action' => $action,
        ]));
    }

    /**
     * @param $template
     * @param bool $throw
     *
     * @return null|string
     */
    public function findTemplate($template, $throw = true)
    {
        $template = $this
            ->get('admin.template.finder')
            ->findTemplate($this->getBundle()->getName(), $this->classNameShort(), $template);

        if (null === $template && $throw) {
            throw new \RuntimeException(sprintf('Template %s not found', $template));
        }

        return $template;
    }

    /**
     * @param string   $view
     * @param array    $parameters
     * @param Response $response
     *
     * @return string
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        return parent::render($view, array_merge($parameters, [
            'admin' => $this,
            'bundle' => $this->getBundle(),
            'adminMenu' => $this->get('admin.menu')->getMenu(),
        ]), $response);
    }

    /**
     * @return Bundle
     */
    abstract protected function getBundle();
}
