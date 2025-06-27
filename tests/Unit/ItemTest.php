<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\Tests\Unit;

use Anisotton\Pagarme\Contracts\Payments\Item;
use Anisotton\Pagarme\Tests\TestCase;

class ItemTest extends TestCase
{
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        $this->item = new Item;
    }

    public function test_can_create_item(): void
    {
        $result = $this->item->item(1000, 'Test Product', 'test-123', 2);

        $this->assertIsArray($result);
        $this->assertEquals(1000, $result['amount']);
        $this->assertEquals('Test Product', $result['description']);
        $this->assertEquals('test-123', $result['code']);
        $this->assertEquals(2, $result['quantity']);
    }

    public function test_can_create_item_with_default_quantity(): void
    {
        $result = $this->item->item(2500, 'Another Product', 'prod-456');

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['quantity']); // default quantity
    }
}
