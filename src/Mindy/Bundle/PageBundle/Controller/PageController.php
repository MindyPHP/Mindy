<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\PageBundle\Controller;

use Mindy\Bundle\MindyBundle\Controller\Controller;
use Mindy\Bundle\PageBundle\Model\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function viewAction($url = null)
    {
        $qs = Page::objects()->published();
        if (empty($url)) {
            $qs->filter(['is_index' => true]);
        } else {
            $qs->filter(['url' => ltrim($url, '/')]);
        }
        /** @var Page $page */
        $page = $qs->get();

        if ($page === null) {
            throw new NotFoundHttpException();
        }

        list($breadcrumbs, $title) = $this->fetchBreadrumbs($page);

        $pager = $this->createPagination($page->getChildrenQuerySet());

        return $this->render($page->findView(), [
            'page' => $page,
            'children' => $pager->paginate(),
            'pager' => $pager->createView(),
            'title' => $title,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * @param Page $model
     *
     * @return array
     */
    protected function fetchBreadrumbs(Page $model): array
    {
        $title = [];
        $breadcrumbs = [];
        if (!$model->is_index) {
            /* @var Page $page */
            $pages = $model->objects()->ancestors()->order(['level'])->all();
            foreach ($pages as $page) {
                $title[] = $page->name;
                $breadcrumbs[] = [
                    'name' => $page->name,
                    'url' => $this->generateUrl('page_view', ['url' => $page->url]),
                ];
            }
            $title[] = $model->name;
            $breadcrumbs[] = [
                'name' => $model->name,
                'url' => $this->generateUrl('page_view', ['url' => $model->url]),
            ];
        }

        return [$breadcrumbs, $title];
    }
}
