<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/10/16
 * Time: 15:48.
 */

namespace Mindy\Bundle\MetaBundle\Library;

use Mindy\Bundle\MetaBundle\Model\Template;
use Mindy\Bundle\MetaBundle\Provider\MetaProvider;
use Mindy\Template\Library;
use Mindy\Template\Renderer;
use Symfony\Component\HttpFoundation\RequestStack;

class MetaLibrary extends Library
{
    protected $request;
    protected $metaProvider;
    protected $template;

    public function __construct(RequestStack $requestStack, MetaProvider $metaProvider, Renderer $template)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->metaProvider = $metaProvider;
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'render_meta' => function ($template = 'meta/meta.html') {
                $meta = $this->metaProvider->getMeta($this->request);
                if (null === $meta) {
                    $meta = [];
                }

                return $this->template->render($template, [
                    'meta' => $meta,
                ]);
            },
            'render_template' => function ($code, array $params = []) {
                /** @var Template $metaTemplate */
                $metaTemplate = Template::objects()->get(['code' => $code]);
                if (null === $metaTemplate) {
                    return '';
                }

                return $this->template->renderString($metaTemplate->content, $params);
            },
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
