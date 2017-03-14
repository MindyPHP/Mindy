<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
