<?php

namespace App\Libraries;

use Zero\Http\Client;

class ProductsPy extends Client
{
    public function vendors(array $params = [])
    {
        return $this->get('vendedores', $params);
    }

    /**
     * @inheritDoc
     */
    public function baseUri(): string
    {
        return 'https://productospy.org/api/';
    }
}
