<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_index_renders()
    {
        $user = User::factory()->create();
        Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole('admin');
        $response = $this->actingAs($user)->get('/admin/sales');
        $response->assertStatus(200);
        $response->assertSee('Penjualan');
    }
}
