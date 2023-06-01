<?php

use UPFlex\MixUp\Core\Base;

test('check authorized self instance', function () {
    $self_instance = Base::isSelfInstance();
    expect($self_instance)->toBeTrue();
});
