<?php

namespace WooRaffles\Woocommerce;

use UPFlex\MixUp\Core\Base;
use UPFlex\MixUp\Utils\TemplateParts;

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

class Template extends Base
{
    use TemplateParts;

    public function __construct()
    {
        self::setTemplateParams();
    }

    protected static function setTemplateParams()
    {
        self::setFolder(WOORAFFLES_DIR . '/views/woocommerce');
    }
}
