<?php

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
