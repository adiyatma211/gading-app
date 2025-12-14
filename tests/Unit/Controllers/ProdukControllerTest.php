<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ProdukController;
use App\Models\Produk;
use App\Models\ProdukBahan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProdukControllerTest extends TestCase
{
    use RefreshDatabase;

    protected ProdukController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProdukController();
    }

    /**
     * Test store method with valid product and materials
     */
    public function test_store_creates_product_with_materials_successfully()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product with Materials',
            'bahan' => [
                [
                    'nama_bahan' => 'Material 1',
                    'harga_per_meter' => 15000,
                    'diskon' => 1000,
                    'total_harga' => 14000
                ],
                [
                    'nama_bahan' => 'Material 2',
                    'harga_per_meter' => 20000,
                    'diskon' => 2000,
                    'total_harga' => 18000
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
        $this->assertArrayHasKey('data', $data);

        // Verify database records
        $this->assertDatabaseHas('produks', [
            'nama_produk' => 'Test Product with Materials',
            'status' => 1
        ]);

        $product = Produk::where('nama_produk', 'Test Product with Materials')->first();
        $this->assertDatabaseHas('produk_bahans', [
            'produk_id' => $product->id,
            'nama_bahan' => 'Material 1',
            'harga_per_meter' => 15000,
            'diskon' => 1000,
            'total_harga' => 14000
        ]);

        $this->assertDatabaseHas('produk_bahans', [
            'produk_id' => $product->id,
            'nama_bahan' => 'Material 2',
            'harga_per_meter' => 20000,
            'diskon' => 2000,
            'total_harga' => 18000
        ]);
    }

    /**
     * Test store method with validation failure
     */
    public function test_store_with_validation_failure_returns_error()
    {
        // Arrange
        $requestData = [
            'nama_produk' => '',  // Invalid: empty name
            'bahan' => [
                [
                    'nama_bahan' => '',  // Invalid: empty material name
                    'harga_per_meter' => -1000,  // Invalid: negative price
                    'diskon' => 'invalid',  // Invalid: not numeric
                    'total_harga' => 'invalid'  // Invalid: not numeric
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act & Assert
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->store($request);
    }

    /**
     * Test store method with database transaction rollback on error
     */
    public function test_store_rolls_back_transaction_on_error()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Test Product',
            'bahan' => [
                [
                    'nama_bahan' => 'Valid Material',
                    'harga_per_meter' => 15000,
                    'diskon' => 1000,
                    'total_harga' => 14000
                ],
                [
                    'nama_bahan' => 'Invalid Material',
                    'harga_per_meter' => 'not_a_number',  // This will cause validation error
                    'diskon' => 1000,
                    'total_harga' => 14000
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act & Assert
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->store($request);

        // Verify no records were created due to rollback
        $this->assertDatabaseMissing('produks', [
            'nama_produk' => 'Test Product'
        ]);
        $this->assertDatabaseMissing('produk_bahans', [
            'nama_bahan' => 'Valid Material'
        ]);
    }

    /**
     * Test edit method with valid product ID
     */
    public function test_edit_with_valid_id_returns_product_with_materials()
    {
        // Arrange
        $product = Produk::factory()->create();
        $material1 = ProdukBahan::factory()->create(['produk_id' => $product->id]);
        $material2 = ProdukBahan::factory()->create(['produk_id' => $product->id]);

        // Act
        $response = $this->controller->edit($product->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Produk ditemukan', $data['message']);
        $this->assertEquals($product->id, $data['data']['id']);
        $this->assertArrayHasKey('bahan', $data['data']);
        $this->assertCount(2, $data['data']['bahan']);
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
    }

    /**
     * Test update method with valid data
     */
    public function test_update_with_valid_data_updates_product_and_materials()
    {
        // Arrange
        $product = Produk::factory()->create(['nama_produk' => 'Original Product']);
        $material1 = ProdukBahan::factory()->create(['produk_id' => $product->id]);
        $material2 = ProdukBahan::factory()->create(['produk_id' => $product->id]);

        $requestData = [
            'nama_produk' => 'Updated Product Name',
            'bahan' => [
                [
                    'id' => $material1->id,  // Update existing material
                    'nama_bahan' => 'Updated Material 1',
                    'harga_per_meter' => 25000,
                    'diskon' => 2000,
                    'total_harga' => 23000
                ],
                [
                    'id' => null,  // Create new material
                    'nama_bahan' => 'New Material',
                    'harga_per_meter' => 30000,
                    'diskon' => 3000,
                    'total_harga' => 27000
                ]
                // Note: material2 is not included, so it should be deleted
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

        // Verify product was updated
        $this->assertDatabaseHas('produks', [
            'id' => $product->id,
            'nama_produk' => 'Updated Product Name'
        ]);

        // Verify material1 was updated
        $this->assertDatabaseHas('produk_bahans', [
            'id' => $material1->id,
            'produk_id' => $product->id,
            'nama_bahan' => 'Updated Material 1',
            'harga_per_meter' => 25000,
            'diskon' => 2000,
            'total_harga' => 23000
        ]);

        // Verify new material was created
        $this->assertDatabaseHas('produk_bahans', [
            'produk_id' => $product->id,
            'nama_bahan' => 'New Material',
            'harga_per_meter' => 30000,
            'diskon' => 3000,
            'total_harga' => 27000
        ]);

        // Verify material2 was deleted
        $this->assertDatabaseMissing('produk_bahans', [
            'id' => $material2->id
        ]);
    }

    /**
     * Test update method with validation failure
     */
    public function test_update_with_validation_failure_returns_error()
    {
        // Arrange
        $product = Produk::factory()->create();

        $requestData = [
            'nama_produk' => '',  // Invalid: empty name
            'bahan' => [
                [
                    'nama_bahan' => '',  // Invalid: empty material name
                    'harga_per_meter' => -1000,  // Invalid: negative price
                    'diskon' => 'invalid',  // Invalid: not numeric
                    'total_harga' => 'invalid'  // Invalid: not numeric
                ]
            ]
        ];

        $request = new Request($requestData);

        // Act & Assert
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->update($request, $product->id);
    }

    /**
     * Test updateBahan method with valid data
     */
    public function test_updateBahan_with_valid_data_updates_material()
    {
        // Arrange
        $material = ProdukBahan::factory()->create([
            'nama_bahan' => 'Original Material',
            'harga_per_meter' => 15000,
            'diskon' => 1000,
            'total_harga' => 14000
        ]);

        $requestData = [
            'nama_bahan' => 'Updated Material',
            'harga_per_meter' => 25000,
            'diskon' => 2000,
            'total_harga' => 23000
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->updateBahan($request, $material->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Bahan berhasil diperbarui', $data['message']);

        // Verify database was updated
        $this->assertDatabaseHas('produk_bahans', [
            'id' => $material->id,
            'nama_bahan' => 'Updated Material',
            'harga_per_meter' => 25000,
            'diskon' => 2000,
            'total_harga' => 23000
        ]);
    }

    /**
     * Test updateBahan method with auto-calculated total_harga
     */
    public function test_updateBahan_calculates_total_harga_automatically()
    {
        // Arrange
        $material = ProdukBahan::factory()->create([
            'nama_bahan' => 'Original Material',
            'harga_per_meter' => 15000,
            'diskon' => 1000,
            'total_harga' => 14000
        ]);

        $requestData = [
            'nama_bahan' => 'Updated Material',
            'harga_per_meter' => 25000,
            'diskon' => 5000,
            'total_harga' => null  // Should be calculated automatically
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->updateBahan($request, $material->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);

        // Verify total_harga was calculated correctly (25000 - 5000 = 20000)
        $this->assertDatabaseHas('produk_bahans', [
            'id' => $material->id,
            'nama_bahan' => 'Updated Material',
            'harga_per_meter' => 25000,
            'diskon' => 5000,
            'total_harga' => 20000  // Calculated automatically
        ]);
    }

    /**
     * Test updateBahan method with invalid material ID
     */
    public function test_updateBahan_with_invalid_id_returns_error()
    {
        // Arrange
        $requestData = [
            'nama_bahan' => 'Updated Material',
            'harga_per_meter' => 25000,
            'diskon' => 2000,
            'total_harga' => 23000
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->updateBahan($request, 999);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Gagal memperbarui bahan', $data['message']);
    }

    /**
     * Test destroy method with valid product ID
     */
    public function test_destroy_with_valid_id_deletes_product_and_materials()
    {
        // Arrange
        $product = Produk::factory()->create();
        $material1 = ProdukBahan::factory()->create(['produk_id' => $product->id]);
        $material2 = ProdukBahan::factory()->create(['produk_id' => $product->id]);

        // Act
        $response = $this->controller->destroy($product->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Produk berhasil dihapus', $data['message']);

        // Verify records were deleted
        $this->assertDatabaseMissing('produks', ['id' => $product->id]);
        $this->assertDatabaseMissing('produk_bahans', ['id' => $material1->id]);
        $this->assertDatabaseMissing('produk_bahans', ['id' => $material2->id]);
    }

    /**
     * Test destroy method with invalid product ID
     */
    public function test_destroy_with_invalid_id_returns_error()
    {
        // Act
        $response = $this->controller->destroy(999);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Gagal menghapus produk', $data['message']);
    }

    /**
     * Test hapusBahan method with valid material ID
     */
    public function test_hapusBahan_with_valid_id_deletes_material()
    {
        // Arrange
        $material = ProdukBahan::factory()->create();

        // Act
        $response = $this->controller->hapusBahan($material->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals('Bahan berhasil dihapus', $data['message']);

        // Verify record was deleted
        $this->assertDatabaseMissing('produk_bahans', ['id' => $material->id]);
    }

    /**
     * Test hapusBahan method with invalid material ID
     */
    public function test_hapusBahan_with_invalid_id_returns_error()
    {
        // Act
        $response = $this->controller->hapusBahan(999);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['status']);
        $this->assertEquals('Bahan tidak ditemukan', $data['message']);
    }

    /**
     * Test material pricing with discounts
     */
    public function test_material_pricing_with_discounts()
    {
        // Arrange
        $requestData = [
            'nama_produk' => 'Product with Discounted Materials',
            'bahan' => [
                [
                    'nama_bahan' => 'Premium Material',
                    'harga_per_meter' => 50000,
                    'diskon' => 10000,  // 20% discount
                    'total_harga' => 40000
                ],
                [
                    'nama_bahan' => 'Standard Material',
                    'harga_per_meter' => 30000,
                    'diskon' => 3000,  // 10% discount
                    'total_harga' => 27000
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

        // Verify discount calculations
        $product = Produk::where('nama_produk', 'Product with Discounted Materials')->first();
        $materials = $product->bahan;

        $premiumMaterial = $materials->where('nama_bahan', 'Premium Material')->first();
        $this->assertEquals(50000, $premiumMaterial->harga_per_meter);
        $this->assertEquals(10000, $premiumMaterial->diskon);
        $this->assertEquals(40000, $premiumMaterial->total_harga);

        $standardMaterial = $materials->where('nama_bahan', 'Standard Material')->first();
        $this->assertEquals(30000, $standardMaterial->harga_per_meter);
        $this->assertEquals(3000, $standardMaterial->diskon);
        $this->assertEquals(27000, $standardMaterial->total_harga);
    }

    /**
     * Test product-material relationships
     */
    public function test_product_material_relationships()
    {
        // Arrange
        $product = Produk::factory()->create();
        $material1 = ProdukBahan::factory()->create(['produk_id' => $product->id]);
        $material2 = ProdukBahan::factory()->create(['produk_id' => $product->id]);
        $material3 = ProdukBahan::factory()->create(['produk_id' => $product->id]);

        // Act
        $response = $this->controller->edit($product->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['status']);

        // Verify relationship is loaded correctly
        $this->assertArrayHasKey('bahan', $data['data']);
        $this->assertCount(3, $data['data']['bahan']);

        // Verify all materials belong to the product
        foreach ($data['data']['bahan'] as $material) {
            $this->assertEquals($product->id, $material['produk_id']);
        }
    }
}
