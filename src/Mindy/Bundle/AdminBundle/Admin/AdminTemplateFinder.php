<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\AdminBundle\Admin;

use Mindy\Finder\TemplateFinderInterface;

/**
 * Class TemplateFinder.
 */
class AdminTemplateFinder
{
    /**
     * Default admin template paths for easy override.
     *
     * @var array
     */
    public $paths = [
        '{bundle}/admin/{admin}/{template}',
        'admin/{bundle}/{admin}/{template}',
        'admin/admin/{template}',
        'admin/{template}',
    ];
    /**
     * @var TemplateFinderInterface
     */
    protected $templateFinder;

    /**
     * TemplateFinder constructor.
     *
     * @param TemplateFinderInterface $templateFinder
     */
    public function __construct(TemplateFinderInterface $templateFinder)
    {
        $this->templateFinder = $templateFinder;
    }

    /**
     * @param $str
     *
     * @return string
     */
    protected function normalizeString($str)
    {
        return trim(strtolower(preg_replace('/(?<![A-Z])[A-Z]/', '_\0', $str)), '_');
    }

    /**
     * @param $bundleName
     * @param $adminName
     * @param $template
     *
     * @return string|void
     */
    public function findTemplate($bundleName, $adminName, $template)
    {
        foreach ($this->paths as $pathTemplate) {
            $path = strtr($pathTemplate, [
                '{bundle}' => strtolower(str_replace('Bundle', '', $bundleName)),
                '{admin}' => strtolower($this->normalizeString(str_replace('Admin', '', $adminName))),
                '{template}' => $template,
            ]);
            if ($this->templateFinder->find($path)) {
                return $path;
            }
        }
    }
}
