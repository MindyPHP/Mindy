<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Template;

/**
 * Class Library.
 */
abstract class Library
{
    /**
     * @var \Mindy\Template\Parser
     */
    protected $parser;
    /**
     * @var \Mindy\Template\TokenStream
     */
    protected $stream;

    /**
     * @return array
     */
    abstract public function getHelpers();

    /**
     * @return array
     */
    abstract public function getTags();

    public function setParser(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    public function setStream(TokenStream $stream)
    {
        $this->stream = $stream;

        return $this;
    }
}
