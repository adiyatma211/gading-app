<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ProdukNewController;
use App\Models\HargaProdukNew;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ProdukNewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected ProdukNewController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProdukNewController();
    }

    /**
     * Test store method with per_meter product type
     */
    public function test_store_creates_per_meter_product_successfully()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product Per Meter',
            'tipe_produk' => 'per_meter',
            'harga' => [
                [
                    'harga' => 15000,
                    'diskon' => 1000,
                    'min_qty' => null,
                    'max_qty' => null,
                    'sisi' => 2,
                    'laminasi' => 'on'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Produk berhasil ditambahkan', $data['message']);

        // Verify database records
        $this->assertDatabaseHas('produks', [
            'nama_produk' => 'Test Product Per Meter',
            'tipe_produk' => 'per_meter',
            'status' => '1'
        ]);

        $this->assertDatabaseHas('harga_produk_new', [
            'harga' => 15000,
            'diskon' => 1000,
            'sisi' => 2,
            'laminasi' => true
        ]);
    }

    /**
     * Test store method with tiered product type
     */
    public function test_store_creates_tiered_product_successfully()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product Tiered',
            'tipe_produk' => 'tiered',
            'harga' => [
                [
                    'harga' => 15000,
                    'diskon' => 0,
                    'min_qty' => 1,
                    'max_qty' => 10,
                    'sisi' => 1,
                    'laminasi' => false
                ],
                [
                    'harga' => 12000,
                    'diskon' => 500,
                    'min_qty' => 11,
                    'max_qty' => 50,
                    'sisi' => 2,
                    'laminasi' => 'on'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);

        // Verify database records
        $this->assertDatabaseHas('produks', [
            'nama_produk' => 'Test Product Tiered',
            'tipe_produk' => 'tiered'
        ]);

        $this->assertDatabaseHas('harga_produk_new', [
            'harga' => 15000,
            'min_qty' => 1,
            'max_qty' => 10,
            'laminasi' => false
        ]);

        $this->assertDatabaseHas('harga_produk_new', [
            'harga' => 12000,
            'min_qty' => 11,
            'max_qty' => 50,
            'laminasi' => true
        ]);
    }

    /**
     * Test store method with flat product type
     */
    public function test_store_creates_flat_product_successfully()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product Flat',
            'tipe_produk' => 'flat',
            'harga' => [
                [
                    'harga' => 50000,
                    'diskon' => 5000,
                    'min_qty' => null,
                    'max_qty' => null,
                    'sisi' => 1,
                    'laminasi' => 'on'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);

        // Verify database records
        $this->assertDatabaseHas('produks', [
            'nama_produk' => 'Test Product Flat',
            'tipe_produk' => 'flat'
        ]);
    }

    /**
     * Test store method with custom product type
     */
    public function test_store_creates_custom_product_successfully()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product Custom',
            'tipe_produk' => 'custom',
            'harga' => [
                [
                    'harga' => 75000,
                    'diskon' => 0,
                    'min_qty' => null,
                    'max_qty' => null,
                    'sisi' => 2,
                    'laminasi' => false
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);

        // Verify database records
        $this->assertDatabaseHas('produks', [
            'nama_produk' => 'Test Product Custom',
            'tipe_produk' => 'custom'
        ]);
    }

    /**
     * Test store method with laminasi boolean conversion
     */
    public function test_store_converts_laminasi_to_boolean()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product Laminasi',
            'tipe_produk' => 'per_meter',
            'harga' => [
                [
                    'harga' => 15000,
                    'diskon' => 0,
                    'sisi' => 1,
                    'laminasi' => 'on'  // Should be converted to true
                ],
                [
                    'harga' => 20000,
                    'diskon' => 1000,
                    'sisi' => 2,
                    'laminasi' => '1'  // Should be converted to true
                ],
                [
                    'harga' => 25000,
                    'diskon' => 2000,
                    'sisi' => 1,
                    'laminasi' => false  // Should remain false
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);

        // Verify laminasi conversion
        $product = Produk::where('nama_produk', 'Test Product Laminasi')->first();
        $prices = $product->hargas;

        $this->assertTrue($prices[0]->laminasi);  // 'on' converted to true
        $this->assertTrue($prices[1]->laminasi);  // '1' converted to true
        $this->assertFalse($prices[2]->laminasi); // false remains false
    }

    /**
     * Test store method with validation failure
     */
    public function test_store_with_validation_failure_returns_error()
    {
        // Arrange
        $requestData = [
            'nama_produk' => '',  // Invalid: empty name
            'tipe_produk' => 'invalid_type',  // Invalid: not in allowed types
            'harga' => [
                [
                    'harga' => -1000,  // Invalid: negative price
                    'diskon' => 'invalid',  // Invalid: not numeric
                    'sisi' => 3  // Invalid: not 1 or 2
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Validasi gagal.', $data['message']);
        $this->assertArrayHasKey('errors', $data);
    }

    /**
     * Test edit method with valid product ID
     */
    public function test_edit_with_valid_id_returns_product_data()
    {
        // Arrange
        $product = Produk::factory()->create();
        HargaProdukNew::factory()->create(['produk_id' => $product->id]);

        // Act
        $response = $this->controller->edit($product->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals($product->id, $data['data']['id']);
        $this->assertArrayHasKey('hargas', $data['data']);
    }

    /**
     * Test edit method with invalid product ID
     */
    public function test_edit_with_invalid_id_returns_404()
    {
        // Act
        $response = $this->controller->edit(999);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Produk tidak ditemukan', $data['message']);
    }

    /**
     * Test update method with valid data
     */
    public function test_update_with_valid_data_updates_product()
    {
        // Arrange
        $product = Produk::factory()->create(['tipe_produk' => 'per_meter']);
        HargaProdukNew::factory()->create(['produk_id' => $product->id]);

        $requestData = [
            'nama_produk' => 'Updated Product Name',
            'tipe_produk' => 'tiered',
            'harga' => [
                [
                    'harga' => 25000,
                    'diskon' => 2000,
                    'min_qty' => 1,
                    'max_qty' => 20,
                    'sisi' => 2,
                    'laminasi' => true  // Use boolean true instead of 'on'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->update($request, $product->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Produk berhasil diperbarui', $data['message']);

        // Verify database was updated
        $this->assertDatabaseHas('produks', [
            'id' => $product->id,
            'nama_produk' => 'Updated Product Name',
            'tipe_produk' => 'tiered'
        ]);

        // Verify old prices were deleted and new ones created
        $this->assertDatabaseHas('harga_produk_new', [
            'produk_id' => $product->id,
            'harga' => 25000,
            'diskon' => 2000,
            'min_qty' => 1,
            'max_qty' => 20,
            'laminasi' => true
        ]);
    }

    /**
     * Test update method with invalid product ID
     */
    public function test_update_with_invalid_id_returns_404()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Updated Product',
            'tipe_produk' => 'per_meter',
            'harga' => [
                [
                    'harga' => 15000,
                    'diskon' => 0,
                    'sisi' => 1,
                    'laminasi' => false
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->update($request, 999);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Produk tidak ditemukan', $data['message']);
    }

    /**
     * Test destroy method with valid product ID
     */
    public function test_destroy_with_valid_id_deletes_product()
    {
        // Arrange
        $product = Produk::factory()->create();
        HargaProdukNew::factory()->create(['produk_id' => $product->id]);

        // Act
        $response = $this->controller->destroy($product->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Produk berhasil dihapus', $data['message']);

        // Verify records were deleted
        $this->assertDatabaseMissing('produks', ['id' => $product->id]);
        $this->assertDatabaseMissing('harga_produk_new', ['produk_id' => $product->id]);
    }

    /**
     * Test destroy method with invalid product ID
     */
    public function test_destroy_with_invalid_id_returns_404()
    {
        // Act
        $response = $this->controller->destroy(999);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Produk tidak ditemukan', $data['message']);
    }

    /**
     * Test destroyHarga method with valid price ID
     */
    public function test_destroyHarga_with_valid_id_deletes_price()
    {
        // Arrange
        $price = HargaProdukNew::factory()->create();

        // Act
        $response = $this->controller->destroyHarga($price->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Harga berhasil dihapus', $data['message']);

        // Verify record was deleted
        $this->assertDatabaseMissing('harga_produk_new', ['id' => $price->id]);
    }

    /**
     * Test destroyHarga method with invalid price ID
     */
    public function test_destroyHarga_with_invalid_id_returns_404()
    {
        // Act
        $response = $this->controller->destroyHarga(999);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Harga tidak ditemukan', $data['message']);
    }

    /**
     * Test updateHarga method with valid data
     */
    public function test_updateHarga_with_valid_data_updates_price()
    {
        // Arrange
        $price = HargaProdukNew::factory()->create(['harga' => 10000]);
        $requestData = [
            'harga' => 15000
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->updateHarga($request, $price->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Harga berhasil diubah', $data['message']);

        // Verify database was updated
        $this->assertDatabaseHas('harga_produk_new', [
            'id' => $price->id,
            'harga' => 15000
        ]);
    }

    /**
     * Test updateHarga method with invalid price ID
     */
    public function test_updateHarga_with_invalid_id_returns_404()
    {
        // Arrange
        $requestData = [
            'harga' => 15000
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->updateHarga($request, 999);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Harga tidak ditemukan', $data['message']);
    }

    /**
     * Test pricing logic with discounts
     */
    public function test_pricing_logic_with_discounts()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product with Discount',
            'tipe_produk' => 'per_meter',
            'harga' => [
                [
                    'harga' => 20000,
                    'diskon' => 3000,  // 15% discount
                    'min_qty' => null,
                    'max_qty' => null,
                    'sisi' => 2,
                    'laminasi' => false
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);

        // Verify discount was saved correctly
        $product = Produk::where('nama_produk', 'Test Product with Discount')->first();
        $price = $product->hargas->first();

        $this->assertEquals(20000, $price->harga);
        $this->assertEquals(3000, $price->diskon);
        $this->assertEquals(17000, $price->harga - $price->diskon);  // Effective price
    }
}
