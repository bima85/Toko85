<?php

namespace Tests\Feature;

use App\Models\StockCard;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\User;
use Tests\TestCase;

class StockCardFeatureTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test: Index page loads successfully
     */
    public function test_stock_card_index_page_loads()
    {
        $this->actingAs($this->user)
            ->get('/stock-card')
            ->assertStatus(200)
            ->assertSeeLivewire('stock-card.stock-card-index');
    }

    /**
     * Test: Create page loads successfully
     */
    public function test_stock_card_create_page_loads()
    {
        $this->actingAs($this->user)
            ->get('/stock-card/create')
            ->assertStatus(200)
            ->assertSeeLivewire('stock-card.stock-card-form');
    }

    /**
     * Test: Show page loads successfully
     */
    public function test_stock_card_show_page_loads()
    {
        $product = Product::factory()->create();
        $stockCard = StockCard::factory()->create(['product_id' => $product->id]);

        $this->actingAs($this->user)
            ->get("/stock-card/{$stockCard->id}")
            ->assertStatus(200)
            ->assertSeeLivewire('stock-card.stock-card-show');
    }

    /**
     * Test: Edit page loads successfully
     */
    public function test_stock_card_edit_page_loads()
    {
        $product = Product::factory()->create();
        $stockCard = StockCard::factory()->create(['product_id' => $product->id]);

        $this->actingAs($this->user)
            ->get("/stock-card/{$stockCard->id}/edit")
            ->assertStatus(200)
            ->assertSeeLivewire('stock-card.stock-card-form');
    }

    /**
     * Test: Create stock card successfully
     */
    public function test_create_stock_card_successfully()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-form')
            ->set('product_id', $product->id)
            ->set('type', 'in')
            ->set('qty', 100)
            ->set('note', 'Test entry')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('stock_cards', [
            'product_id' => $product->id,
            'type' => 'in',
            'qty' => 100,
        ]);
    }

    /**
     * Test: Update stock card successfully
     */
    public function test_update_stock_card_successfully()
    {
        $product = Product::factory()->create();
        $stockCard = StockCard::factory()->create(['product_id' => $product->id]);

        $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-form', ['stockCard' => $stockCard])
            ->set('qty', 200)
            ->set('note', 'Updated note')
            ->call('save')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('stock_cards', [
            'id' => $stockCard->id,
            'qty' => 200,
            'note' => 'Updated note',
        ]);
    }

    /**
     * Test: Validation fails without product
     */
    public function test_validation_fails_without_product()
    {
        $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-form')
            ->set('type', 'in')
            ->set('qty', 100)
            ->call('save')
            ->assertHasErrors('product_id');
    }

    /**
     * Test: Validation fails with invalid quantity
     */
    public function test_validation_fails_with_invalid_quantity()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-form')
            ->set('product_id', $product->id)
            ->set('type', 'in')
            ->set('qty', 0)
            ->call('save')
            ->assertHasErrors('qty');
    }

    /**
     * Test: Delete stock card successfully
     */
    public function test_delete_stock_card_successfully()
    {
        $product = Product::factory()->create();
        $stockCard = StockCard::factory()->create(['product_id' => $product->id]);

        $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-index')
            ->call('deleteStockCard', $stockCard->id)
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('stock_cards', [
            'id' => $stockCard->id,
        ]);
    }

    /**
     * Test: Search functionality works
     */
    public function test_search_functionality_works()
    {
        $product = Product::factory()->create(['name' => 'Test Product']);
        $stockCard = StockCard::factory()->create(['product_id' => $product->id]);

        $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-index')
            ->set('search', 'Test Product')
            ->assertSee($product->name);
    }

    /**
     * Test: Filter by type works
     */
    public function test_filter_by_type_works()
    {
        $product = Product::factory()->create();
        $inCard = StockCard::factory()->create(['product_id' => $product->id, 'type' => 'in']);
        $outCard = StockCard::factory()->create(['product_id' => $product->id, 'type' => 'out']);

        $response = $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-index')
            ->set('filter_type', 'in');

        // Should only show 'in' type
        $this->assertCount(1, $response->viewData('stockCards'));
    }

    /**
     * Test: Pagination works
     */
    public function test_pagination_works()
    {
        $product = Product::factory()->create();
        StockCard::factory(25)->create(['product_id' => $product->id]);

        $this->actingAs($this->user)
            ->livewire('stock-card.stock-card-index')
            ->set('per_page', 10)
            ->assertSeeHtml('pagination');
    }

    /**
     * Test: Unauthorized access is denied
     */
    public function test_unauthorized_access_denied()
    {
        $this->get('/stock-card')
            ->assertRedirect('/login');
    }
}
