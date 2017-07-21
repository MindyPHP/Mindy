<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\SeoBundle\EventListener;

use Mindy\Bundle\SeoBundle\Provider\SeoProvider;
use Mindy\Orm\ModelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class SeoEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;
    /**
     * @var SeoProvider
     */
    protected $metaProvider;

    /**
     * MetaEventSubscriber constructor.
     *
     * @param SeoProvider  $metaProvider
     * @param RequestStack $requestStack
     */
    public function __construct(SeoProvider $metaProvider, RequestStack $requestStack = null)
    {
        $this->metaProvider = $metaProvider;
        $this->request = $requestStack->getMasterRequest();
    }

    protected function getHost()
    {
        if ($this->request) {
            return $this->request->getHost();
        }

        return $_SERVER['HTTP_HOST'];
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
//        dump(['onPreSetData' => $event]);
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        /** @var ModelInterface $source */
        $source = $event->getData();
        // Чтоб получить URL нужно выполнить сохранение, но сохранение
        // выполняется следующим шагом на уровне
        // $model = $form->getData();
        // $model->save();
        dump(['onPostSubmit' => $event]);
        die;
    }
}
