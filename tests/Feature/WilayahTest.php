<?php

namespace Tests\Feature;

use App\Models\MstWilayah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WilayahTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('mst_role')->insert([
            'id_role' => 2,
            'nama_role' => 'Operator', 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_halaman_index_wilayah_dapat_diakses(): void
    {
        $wilayah = MstWilayah::create([
            'id_wilayah' => '14',
            'nama_wilayah' => 'PROVINSI RIAU',
            'level_wilayah' => 1,
        ]);

        $user = User::factory()->create([
            'id_wilayah' => $wilayah->id_wilayah,
            'id_role' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('admin.wilayah.index'));
        
        $response->assertStatus(200);
    }

   public function test_wilayah_baru_dapat_disimpan(): void
    {
        $wilayah = MstWilayah::create([
            'id_wilayah' => '14',
            'nama_wilayah' => 'PROVINSI RIAU',
            'level_wilayah' => 1,
        ]);

        $user = User::factory()->create([
            'id_wilayah' => $wilayah->id_wilayah,
            'id_role' => 2,
        ]);

        $data = [
            'id_wilayah' => '1479', // Pastikan ID dan Nama Wilayah saja
            'nama_wilayah' => 'KOTA BARU',
            'level_wilayah' => 2,
        ];

        $response = $this->actingAs($user)
            ->post(route('admin.wilayah.store'), $data);

        $response->assertRedirect(route('admin.wilayah.index'));
        $this->assertDatabaseHas('mst_wilayah', ['nama_wilayah' => 'KOTA BARU']);
    }
}