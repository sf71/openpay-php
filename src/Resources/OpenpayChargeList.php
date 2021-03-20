<?php

namespace Openpay\Resources;

use Openpay\OpenpayApiDerivedResource;

class OpenpayChargeList extends OpenpayApiDerivedResource
{

    public function create($params) {
        return $this->add($params);
    }

}
