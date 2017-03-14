<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Mindy\Bundle\MailBundle\SwiftMailer\Plugins;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;

/**
 * Class DefaultSubjectPrefixPlugin
 */
class DefaultSubjectPrefixPlugin implements Swift_Events_SendListener
{
    /**
     * @var string
     */
    private $defaultSubjectPrefix;

    /**
     * List if IDs of messages which got sender information set
     * by this plugin.
     *
     * @var string[]
     */
    private $handledMessageIds = [];

    /**
     * @param string $defaultSubjectPrefix
     */
    public function __construct($defaultSubjectPrefix)
    {
        $this->defaultSubjectPrefix = $defaultSubjectPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        // replace sender
        if (!count($message->getFrom())) {
            $this->handledMessageIds[$message->getId()] = $message->getSubject();
            $message->setSubject(sprintf(
                '%s %s',
                $this->defaultSubjectPrefix, $message->getSubject()
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        // restore original headers
        $id = $message->getId();
        if (array_key_exists($id, $this->handledMessageIds)) {
            $message->setSubject($this->handledMessageIds[$id]);
            unset($this->handledMessageIds[$id]);
        }
    }
}
