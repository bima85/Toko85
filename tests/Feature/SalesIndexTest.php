<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_sales_index()
    {
        // Ensure admin role exists
        Role::firstOrCreate(['name' => 'admin']);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('admin.sales'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Penjualan');
        $response->assertSee('Buat Penjualan');
    }
}
