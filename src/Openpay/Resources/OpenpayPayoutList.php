<?php

namespace Openpay\Resources;

use Openpay\OpenpayApiDerivedResource;

class OpenpayPayoutList extends OpenpayApiDerivedResource {
    public function create($params) {
        return $this->add($params);
    }
}
