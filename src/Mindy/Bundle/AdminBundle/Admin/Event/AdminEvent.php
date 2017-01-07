<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 22:44
 */

namespace Mindy\Bundle\AdminBundle\Admin\Event;

use Mindy\Orm\ModelInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AdminEvent
 * @package Mindy\Bundle\AdminBundle\Admin\Event
 */
class AdminEvent extends Event
{
    /**
     * @var ModelInterface
     */
    protected $instance;

    /**
     * AdminEvent constructor.
     * @param ModelInterface $instance
     */
    public function __construct(ModelInterface $instance)
    {
        $this->instance = $instance;
    }

    /**
     * @return ModelInterface
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
