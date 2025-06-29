<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Endpoints;

use Anisotton\Pagarme\Utils\ApiAdapter;

class Recipient extends ApiAdapter
{
    public function create(array $data)
    {
        return $this->post('recipients', $data);
    }

    public function find($id)
    {
        return $this->get("recipients/{$id}");
    }

    public function update($id, array $data)
    {
        return $this->put("recipients/{$id}", $data);
    }

    public function all()
    {
        return $this->get('recipients');
    }
}
