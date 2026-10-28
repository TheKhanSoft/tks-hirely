<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BasicInfo;
use App\Livewire\Admin\EntityManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EntityManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_basic_infos(): void
    {
        // Seed roles & permissions
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        // Create a registrar user
        $admin = User::factory()->create();
        $admin->assignRole('registrar');

        // Create an applicant user
        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');

        $this->actingAs($admin);

        // Test Livewire component - CREATE basicInfo
        Livewire::test(EntityManagement::class)
            ->call('setTab', 'basic_infos')
            ->call('openCreateBasicInfoForm')
            ->set('bi_user_id', $applicant->id)
            ->set('bi_first_name', 'Test')
            ->set('bi_last_name', 'User')
            ->set('bi_cnic_number', '12345-1234567-1')
            ->set('bi_dob', '1990-01-01')
            ->call('saveBasicInfo')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('basic_infos', [
            'user_id' => $applicant->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'cnic_number' => '12345-1234567-1',
        ]);

        $basicInfo = BasicInfo::where('user_id', $applicant->id)->first();
        $this->assertNotNull($basicInfo);

        // Test Livewire component - UPDATE basicInfo
        Livewire::test(EntityManagement::class)
            ->call('setTab', 'basic_infos')
            ->call('openEditBasicInfoForm', $basicInfo->id)
            ->assertSet('bi_first_name', 'Test')
            ->set('bi_first_name', 'UpdatedName')
            ->call('saveBasicInfo')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('basic_infos', [
            'id' => $basicInfo->id,
            'first_name' => 'UpdatedName',
        ]);

        // Test Livewire component - DELETE basicInfo
        Livewire::test(EntityManagement::class)
            ->call('setTab', 'basic_infos')
            ->call('deleteBasicInfo', $basicInfo->id);

        $this->assertDatabaseMissing('basic_infos', [
            'id' => $basicInfo->id,
        ]);
    }
}
