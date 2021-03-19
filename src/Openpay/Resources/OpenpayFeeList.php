<?php

namespace Openpay\Resources;

use Openpay\OpenpayApiDerivedResource;

class OpenpayFeeList extends OpenpayApiDerivedResource
{

    public function create($params) {
        return $this->add($params);
    }

}
