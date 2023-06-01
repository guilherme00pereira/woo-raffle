<?php

namespace UPFlex\MixUp\Utils;

trait Message
{
    use Email;

    /**
     * @return array
     */
    abstract protected static function send(): array;
}
