<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin\Event;

use Mindy\Orm\ModelInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AdminEvent
 */
class AdminEvent extends Event
{
    /**
     * @var ModelInterface
     */
    protected $instance;

    /**
     * AdminEvent constructor.
     *
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
