<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Models\Product;

class ProductTest extends TestCase {
    private Product $product;

    protected function setUp(): void {
        $this->product = new Product(
            1,
            "Test Product",
            2.50,
            "Test beschrijving",
            "test.jpg",
            10,
            1
        );
    }

    public function testProductCreation(): void {
        $this->assertEquals(1, $this->product->getId());
        $this->assertEquals("Test Product", $this->product->getNaam());
        $this->assertEquals(2.50, $this->product->getPrijs());
        $this->assertEquals("Test beschrijving", $this->product->getBeschrijving());
        $this->assertEquals("test.jpg", $this->product->getAfbeelding());
        $this->assertEquals(10, $this->product->getVoorraad());
        $this->assertEquals(1, $this->product->getVakNummer());
    }

    public function testVoorraadVermindering(): void {
        $this->assertTrue($this->product->verminderVoorraad(2));
        $this->assertEquals(8, $this->product->getVoorraad());
        
        // Test voor onvoldoende voorraad
        $this->assertFalse($this->product->verminderVoorraad(10));
        $this->assertEquals(8, $this->product->getVoorraad());
    }

    public function testVoorraadVerhoging(): void {
        $this->product->verhoogVoorraad(5);
        $this->assertEquals(15, $this->product->getVoorraad());
    }
} 