<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Endpoints;

use Anisotton\Pagarme\Utils\ApiAdapter;

class Plan extends ApiAdapter
{
    /**
     * Criar plano
     */
    public function create(array $data)
    {
        return $this->post('plans', $data);
    }

    /**
     * Obter plano
     */
    public function find(string $id)
    {
        return $this->get("plans/{$id}");
    }

    /**
     * Listar planos
     */
    public function all(array $queryParams = [])
    {
        return $this->get('plans', $queryParams);
    }

    /**
     * Atualizar plano
     */
    public function update(string $id, array $data)
    {
        return $this->put("plans/{$id}", $data);
    }

    /**
     * Deletar plano
     */
    public function deletePlan(string $id)
    {
        return $this->delete("plans/{$id}");
    }

    /**
     * Adicionar item ao plano
     */
    public function addItem(string $id, array $data)
    {
        return $this->post("plans/{$id}/items", $data);
    }

    /**
     * Atualizar item do plano
     */
    public function updateItem(string $id, string $itemId, array $data)
    {
        return $this->put("plans/{$id}/items/{$itemId}", $data);
    }

    /**
     * Remover item do plano
     */
    public function deleteItem(string $id, string $itemId)
    {
        return $this->delete("plans/{$id}/items/{$itemId}");
    }

    /**
     * Listar itens do plano
     */
    public function getItems(string $id)
    {
        return $this->get("plans/{$id}/items");
    }
}
