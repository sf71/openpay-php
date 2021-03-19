<?php

namespace Openpay\Resources;

use Openpay\OpenpayApiDerivedResource;

class OpenpayPseList extends OpenpayApiDerivedResource {

    public function create($params) {
        return $this->add($params);
    }

}
