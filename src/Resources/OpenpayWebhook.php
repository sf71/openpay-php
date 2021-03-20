<?php

namespace Openpay\Resources;

use Openpay\OpenpayApiResourceBase;

/**
 * Openpay API v1 Client for PHP (version 1.0.0)
 * 
 * Copyright © Openpay SAPI de C.V. All rights reserved.
 * http://www.openpay.mx/
 * soporte@openpay.mx
 */
class OpenpayWebhook extends OpenpayApiResourceBase
{

    protected $url;
    protected $event_types;

    public function save() {
        return $this->_update();
    }

    public function delete() {
        $this->_delete();
    }

}
