<?php

namespace Tests\Feature;

use App\Models\LawyerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_lawyer_can_update_their_profile_and_contact_details(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->put(route('admin.profile.update'), [
            'name' => 'Jordan Lee',
            'email' => 'jordan@example.com',
            'professional_title' => 'Barrister and Solicitor',
            'short_bio' => 'Clear guidance for difficult decisions.',
            'biography' => 'A full professional biography.',
            'education' => 'Juris Doctor, University of the Philippines.',
            'professional_experience' => 'Private practice since 2018.',
            'phone' => '+886 2 1234 5678',
            'public_email' => 'office@example.com',
            'education' => 'Juris Doctor, University of the Philippines.',
            'professional_experience' => 'Private practice since 2018.',
            'office_address' => 'Taipei City',
        ])->assertRedirect(route('admin.profile.edit'));

        $this->assertSame('Jordan Lee', $lawyer->fresh()->name);
        $this->assertDatabaseHas('lawyer_profiles', [
            'user_id' => $lawyer->id,
            'professional_title' => 'Barrister and Solicitor',
            'public_email' => 'office@example.com',
        ]);
    }

    public function test_lawyer_can_upload_a_portrait(): void
    {
        Storage::fake('public');
        $lawyer = User::factory()->create(['is_lawyer' => true]);

        $this->actingAs($lawyer)->put(route('admin.profile.update'), [
            'name' => $lawyer->name,
            'email' => $lawyer->email,
            'photo' => UploadedFile::fake()->create('portrait.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('admin.profile.edit'));

        $profile = LawyerProfile::query()->where('user_id', $lawyer->id)->firstOrFail();
        $this->assertNotEmpty($profile->photo_path);
        Storage::disk('public')->assertExists($profile->photo_path);
    }
}
