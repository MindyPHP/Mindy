<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Template\Adapter;

/**
 * Interface Adapter.
 */
interface Adapter
{
    /**
     * @param $path
     *
     * @return mixed
     */
    public function isReadable($path);

    /**
     * @param $path
     *
     * @return mixed
     */
    public function lastModified($path);

    /**
     * @param $path
     *
     * @return mixed
     */
    public function getContents($path);
}
