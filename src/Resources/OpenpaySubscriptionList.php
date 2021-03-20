<?php

namespace Openpay\Resources;

use Openpay\OpenpayApiDerivedResource;

class OpenpaySubscriptionList extends OpenpayApiDerivedResource {
    public function create($params) {
        return $this->add($params);
    }
}
