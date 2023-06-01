<?php

namespace UPFlex\MixUp\Utils;

trait Site
{
    /**
     * @return int
     */
    protected static function getSiteId(): int
    {
        $blog_id = 1;

        if (function_exists('get_current_blog_id')) {
            $blog_id = get_current_blog_id();
        }

        return $blog_id;
    }
}
