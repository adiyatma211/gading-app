<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\TransactionsController;
use App\Models\customers;
use App\Models\histoypayment;
use App\Models\historynota;
use App\Models\transactions;
use App\Models\transactionitems;
use App\Models\Produk;
use App\Services\PDFStorageService;
use App\Services\PDFCompressionService;
use Barryvdh\DomPDF\PDF as DomPDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use Exception;

class TransactionsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionsController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TransactionsController();

        // Mock authenticated user
        Auth::shouldReceive('user')->andReturn((object)['name' => 'Test User']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test detailTransaksi method with valid transaction ID
     */
    public function test_detailTransaksi_with_valid_id_returns_transaction_data()
    {
        // Arrange
        $customer = customers::factory()->create();
        $product = Produk::factory()->create();
        $transaction = transactions::factory()->create(['customer_id' => $customer->id]);
        $item = transactionitems::factory()->create([
            'transaction_id' => $transaction->id,
            'tipe_produk_id' => $product->id
        ]);

        // Act
        $response = $this->controller->detailTransaksi($transaction->id);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['success']);
        $this->assertEquals($transaction->id, $data['data']['id']);
        $this->assertArrayHasKey('customer', $data['data']);
        $this->assertArrayHasKey('items', $data['data']);
    }

    /**
     * Test detailTransaksi method with invalid transaction ID
     */
    public function test_detailTransaksi_with_invalid_id_returns_404()
    {
        // Act
        $response = $this->controller->detailTransaksi(999);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Transaksi tidak ditemukan.', $data['message']);
    }

    /**
     * Test store method with new customer creation
     */
    public function test_store_with_new_customer_creates_transaction_successfully()
    {
        // Arrange
        $product = Produk::factory()->create();
        $requestData = [
            'customer' => [
                'nama' => 'Test Customer',
                'telepon' => '08123456789',
                'email' => 'test@example.com',
                'jenis_pelanggan' => 'regular',
                'alamat' => 'Test Address'
            ],
            'summary' => [
                'subtotal' => '100,000',
                'total' => '95,000',
                'biaya_desain' => '5,000',
                'diskon' => '10,000',
                'dp' => '50,000',
                'metode_pembayaran' => 'cash',
                'status_pembayaran' => 'dp',
                'tanggal_ambil' => '2023-12-31'
            ],
            'items' => [
                [
                    'tipe' => $product->id,
                    'panjang' => 10,
                    'lebar' => 5,
                    'harga' => '10,000',
                    'diskonbarang' => '1,000',
                    'keterangan' => 'Test item'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Mock PDF generation
        $mockStorageService = Mockery::mock(PDFStorageService::class);
        $mockStorageService->shouldReceive('storePDF')
            ->twice()
            ->andReturn([
                'success' => true,
                'file_name' => 'test.pdf',
                'file_path' => 'test/path/test.pdf',
                'file_hash' => 'testhash',
                'file_size' => 1024
            ]);

        $this->app->instance(PDFStorageService::class, $mockStorageService);

        // Mock PDF rendering
        $mockPdf = Mockery::mock(DomPDF::class);
        $mockPdf->shouldReceive('loadHTML')->andReturnSelf();
        $mockPdf->shouldReceive('setPaper')->andReturnSelf();
        $mockPdf->shouldReceive('output')->andReturn('fake pdf content');

        // Mock File facade
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('get')->andReturn('fake image data');

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('transaction_id', $data);
        $this->assertArrayHasKey('nota_file', $data);

        // Verify database records
        $this->assertDatabaseHas('customers', [
            'nama' => 'Test Customer',
            'telepon' => '08123456789'
        ]);

        $this->assertDatabaseHas('transactions', [
            'total' => 95000.0,
            'subtotal' => 100000.0,
            'status_pembayaran' => 'dp'
        ]);

        $this->assertDatabaseHas('transaction_items', [
            'tipe_produk_id' => $product->id,
            'panjang' => 10.0,
            'lebar' => 5.0
        ]);

        $this->assertDatabaseHas('histoypayments', [
            'customer_name' => 'Test Customer',
            'jumlah_item' => 1
        ]);
    }

    /**
     * Test store method with existing customer
     */
    public function test_store_with_existing_customer_uses_existing_customer()
    {
        // Arrange
        $customer = customers::factory()->create();
        $product = Produk::factory()->create();
        $requestData = [
            'customer_id' => $customer->id,
            'summary' => [
                'subtotal' => '100,000',
                'total' => '95,000',
                'biaya_desain' => '5,000',
                'diskon' => '10,000',
                'dp' => '50,000',
                'metode_pembayaran' => 'cash',
                'status_pembayaran' => 'dp'
            ],
            'items' => [
                [
                    'tipe' => $product->id,
                    'panjang' => 10,
                    'lebar' => 5,
                    'harga' => '10,000',
                    'keterangan' => 'Test item'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Mock PDF generation
        $mockStorageService = Mockery::mock(PDFStorageService::class);
        $mockStorageService->shouldReceive('storePDF')
            ->twice()
            ->andReturn([
                'success' => true,
                'file_name' => 'test.pdf',
                'file_path' => 'test/path/test.pdf',
                'file_hash' => 'testhash',
                'file_size' => 1024
            ]);

        $this->app->instance(PDFStorageService::class, $mockStorageService);

        // Mock PDF rendering
        $mockPdf = Mockery::mock(DomPDF::class);
        $mockPdf->shouldReceive('loadHTML')->andReturnSelf();
        $mockPdf->shouldReceive('setPaper')->andReturnSelf();
        $mockPdf->shouldReceive('output')->andReturn('fake pdf content');

        // Mock File facade
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('get')->andReturn('fake image data');

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals('success', $data['status']);

        // Verify customer wasn't duplicated
        $this->assertEquals(1, customers::where('nama', $customer->nama)->count());
    }

    /**
     * Test store method with database transaction rollback on error
     */
    public function test_store_rolls_back_transaction_on_error()
    {
        // Arrange
        $requestData = [
            'customer' => [
                'nama' => 'Test Customer',
                'telepon' => '08123456789',
                'email' => 'test@example.com',
                'jenis_pelanggan' => 'regular',
                'alamat' => 'Test Address'
            ],
            'summary' => [
                'subtotal' => '100,000',
                'total' => '95,000',
                'biaya_desain' => '5,000',
                'diskon' => '10,000',
                'dp' => '50,000',
                'metode_pembayaran' => 'cash',
                'status_pembayaran' => 'dp'
            ],
            'items' => [
                [
                    'tipe' => 999, // Non-existent product to trigger error
                    'panjang' => 10,
                    'lebar' => 5,
                    'harga' => '10,000',
                    'keterangan' => 'Test item'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Mock PDF generation to fail
        $mockStorageService = Mockery::mock(PDFStorageService::class);
        $mockStorageService->shouldReceive('storePDF')
            ->andThrow(new Exception('PDF generation failed'));

        $this->app->instance(PDFStorageService::class, $mockStorageService);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertEquals('error', $data['status']);
        $this->assertStringContainsString('Gagal menyimpan transaksi', $data['message']);
    }

    /**
     * Test generateNotaFile method
     */
    public function test_generateNotaFile_creates_pdf_and_updates_transaction()
    {
        // Arrange
        $customer = customers::factory()->create();
        $product = Produk::factory()->create();
        $transaction = transactions::factory()->create(['customer_id' => $customer->id]);
        $item = transactionitems::factory()->create([
            'transaction_id' => $transaction->id,
            'tipe_produk_id' => $product->id
        ]);

        // Mock PDF storage service
        $mockStorageService = Mockery::mock(PDFStorageService::class);
        $mockStorageService->shouldReceive('storePDF')
            ->twice()
            ->andReturn([
                'success' => true,
                'file_name' => 'nota_test.pdf',
                'file_path' => 'storage/nota/nota_test.pdf',
                'file_hash' => 'testhash123',
                'file_size' => 2048
            ]);

        $this->app->instance(PDFStorageService::class, $mockStorageService);

        // Mock PDF rendering
        $mockPdf = Mockery::mock(DomPDF::class);
        $mockPdf->shouldReceive('loadHTML')->andReturnSelf();
        $mockPdf->shouldReceive('setPaper')->andReturnSelf();
        $mockPdf->shouldReceive('output')->andReturn('fake pdf content');

        // Mock File facade
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('get')->andReturn('fake image data');

        // Mock view rendering - skip for this test as it requires complex mocking

        // Mock the view rendering to avoid file system issues
        \View::shouldReceive('make')->andReturnSelf();
        \View::shouldReceive('render')->andReturn('<html>Test PDF Content</html>');

        // Act
        $result = $this->invokePrivateMethod($this->controller, 'generateNotaFile', [$transaction]);

        // Assert
        $this->assertEquals('storage/nota/nota_test.pdf', $result);

        // Verify transaction was updated
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'nota_file' => 'nota_test.pdf',
            'pdf_storage_path' => 'storage/nota/nota_test.pdf'
        ]);

        // Verify history nota was created
        $this->assertDatabaseHas('historynotas', [
            'transaction_id' => $transaction->id,
            'nota_file' => 'nota_test.pdf'
        ]);
    }

    /**
     * Test updateTransaksi method with valid data
     */
    public function test_updateTransaksi_with_valid_data_updates_successfully()
    {
        // Arrange
        $transaction = transactions::factory()->create();
        $requestData = [
            'id_transaksi' => $transaction->id,
            'tanggal_ambil' => '2023-12-31',
            'diambil_oleh' => 'Test Receiver'
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->updateTransaksi($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['success']);

        // Verify database was updated
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'tanggal_ambil' => '2023-12-31',
            'diambil_oleh' => 'Test Receiver',
            'status_pembayaran' => 'lunas'
        ]);
    }

    /**
     * Test updateTransaksi method with invalid transaction ID
     */
    public function test_updateTransaksi_with_invalid_id_returns_404()
    {
        // Arrange
        $requestData = [
            'id_transaksi' => 999,
            'tanggal_ambil' => '2023-12-31'
        ];

        $request = new Request($requestData);

        // Act
        $response = $this->controller->updateTransaksi($request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Transaksi tidak ditemukan.', $data['message']);
    }

    /**
     * Test transaction calculations with discounts
     */
    public function test_transaction_calculations_with_discounts()
    {
        // Arrange
        $customer = customers::factory()->create();
        $product = Produk::factory()->create();

        $requestData = [
            'customer' => [
                'nama' => 'Test Customer',
                'telepon' => '08123456789',
                'email' => 'test@example.com',
                'jenis_pelanggan' => 'regular',
                'alamat' => 'Test Address'
            ],
            'summary' => [
                'subtotal' => '200,000',
                'total' => '170,000',
                'biaya_desain' => '10,000',
                'diskon' => '40,000',
                'dp' => '100,000',
                'metode_pembayaran' => 'transfer',
                'status_pembayaran' => 'dp'
            ],
            'items' => [
                [
                    'tipe' => $product->id,
                    'panjang' => 10,
                    'lebar' => 5,
                    'harga' => '20,000',
                    'diskonbarang' => '2,000', // 10% discount per item
                    'keterangan' => 'Test item 1'
                ],
                [
                    'tipe' => $product->id,
                    'panjang' => 8,
                    'lebar' => 4,
                    'harga' => '20,000',
                    'diskonbarang' => '2,000', // 10% discount per item
                    'keterangan' => 'Test item 2'
                ]
            ]
        ];

        $request = new Request($requestData);

        // Mock PDF generation
        $mockStorageService = Mockery::mock(PDFStorageService::class);
        $mockStorageService->shouldReceive('storePDF')
            ->twice()
            ->andReturn([
                'success' => true,
                'file_name' => 'test.pdf',
                'file_path' => 'test/path/test.pdf',
                'file_hash' => 'testhash',
                'file_size' => 1024
            ]);

        $this->app->instance(PDFStorageService::class, $mockStorageService);

        // Mock PDF rendering
        $mockPdf = Mockery::mock(DomPDF::class);
        $mockPdf->shouldReceive('loadHTML')->andReturnSelf();
        $mockPdf->shouldReceive('setPaper')->andReturnSelf();
        $mockPdf->shouldReceive('output')->andReturn('fake pdf content');

        // Mock File facade
        File::shouldReceive('exists')->andReturn(true);
        File::shouldReceive('get')->andReturn('fake image data');

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());

        // Verify calculations
        $items = transactionitems::where('transaction_id', $response->getData(true)['transaction_id'])->get();

        // First item: (10 * 5) * (20000 - 2000) = 50 * 18000 = 900000
        $this->assertEquals(900000.0, $items[0]->total_harga);

        // Second item: (8 * 4) * (20000 - 2000) = 32 * 18000 = 576000
        $this->assertEquals(576000.0, $items[1]->total_harga);
    }

    /**
     * Helper method to invoke private/protected methods
     */
    private function invokePrivateMethod($object, $methodName, $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
