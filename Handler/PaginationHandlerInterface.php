<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Pagination\Handler;

/**
 * Interface PaginationHandlerInterface.
 */
interface PaginationHandlerInterface
{
    /**
     * @param $key
     * @param $defaultPageSize
     *
     * @return int
     */
    public function getPageSize($key, $defaultPageSize);

    /**
     * @param $key
     * @param int $defaultPage
     *
     * @return int
     */
    public function getPage($key, $defaultPage = 1);

    /**
     * @param $key
     * @param $value
     *
     * @return string
     */
    public function getUrlForQueryParam($key, $value);

    /**
     * @param callable $callback
     */
    public function setIncorrectPageCallback(callable $callback);

    /**
     * Throw exception or redirect user to correct page
     * Example for redirect:
     * function ($handler) {
     *      header("Location: " . $handler->getUrl(1));
     *      exit();
     * }.
     *
     * or throw not found exception:
     * function ($handler) {
     *      throw new NotFoundHttpException();
     * }
     */
    public function wrongPageCallback();
}
