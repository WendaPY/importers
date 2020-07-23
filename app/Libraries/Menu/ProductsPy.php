<?php

namespace App\Libraries\Menu;

use App\Libraries\Menu\Client;

class ProductsPy extends Client
{
    public function vendors(array $params = [])
    {
        return $this->get('vendedores', $params);
    }

    /**
     * Base url for client.
     *
     * @return string
     */
    public function baseUri()
    {
        return 'https://productospy.org/api/';
    }
}
