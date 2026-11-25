<?php

namespace Tests\Feature;

use App\Models\Law;
use App\Models\LawCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LawLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_law_library_searches_and_filters_database_records(): void
    {
        $family = LawCategory::query()->create([
            'category_name' => 'Family and Marriage Law',
            'slug' => 'family-and-marriage-law',
            'description' => 'Family law references.',
        ]);
        $criminal = LawCategory::query()->create([
            'category_name' => 'Criminal Law',
            'slug' => 'criminal-law',
            'description' => 'Criminal law references.',
        ]);
        $ra = Law::query()->create([
            'law_category_id' => $family->id,
            'slug' => 'sample-family-code',
            'law_type' => 'Republic Act',
            'law_number' => '9262',
            'official_title' => 'Sample Family Protection Act',
            'short_description' => 'A searchable family law reference.',
            'date_approved' => '2004-03-08',
            'official_source_url' => 'https://lawphil.net/example.html',
            'is_published' => true,
        ]);
        Law::query()->create([
            'law_category_id' => $criminal->id,
            'slug' => 'sample-criminal-statute',
            'law_type' => 'Republic Act',
            'law_number' => '9999',
            'official_title' => 'Sample Criminal Statute',
            'is_published' => true,
        ]);
        Law::query()->create([
            'law_category_id' => $family->id,
            'slug' => 'private-draft',
            'law_type' => 'Republic Act',
            'law_number' => '10000',
            'official_title' => 'Private Draft Law',
            'is_published' => false,
        ]);

        $this->get(route('laws.index', ['q' => '9262', 'category' => $family->slug]))
            ->assertOk()->assertSee('Sample Family Protection Act')->assertDontSee('Sample Criminal Statute')->assertDontSee('Private Draft Law');
        $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('laws.index', ['q' => '9262', 'category' => $family->slug]))
            ->assertOk()->assertSee('Sample Family Protection Act')->assertDontSee('Main navigation');
        $this->get(route('laws.show', $ra))
            ->assertOk()->assertSee('The text of this law has not been added to this library.')
            ->assertSee('https://lawphil.net/example.html');
        $this->get('/philippine-laws/private-draft')->assertNotFound();
    }

    public function test_only_lawyer_accounts_can_manage_law_references(): void
    {
        $this->get(route('admin.laws.index'))->assertRedirect(route('admin.login'));
        $nonLawyer = User::factory()->create(['is_lawyer' => false]);
        $this->actingAs($nonLawyer)->get(route('admin.laws.index'))->assertForbidden();
    }

    public function test_lawyer_can_create_publish_edit_and_delete_a_law_reference(): void
    {
        $lawyer = User::factory()->create(['is_lawyer' => true]);
        $category = LawCategory::query()->create([
            'category_name' => 'Civil Law',
            'slug' => 'civil-law',
            'description' => 'Civil law references.',
        ]);
        $payload = [
            'law_category_id' => $category->id,
            'law_type' => 'Republic Act',
            'law_number' => '386',
            'official_title' => 'Civil Code of the Philippines',
            'short_description' => 'A civil law reference.',
            'date_approved' => '1949-06-18',
            'official_source_url' => 'https://lawphil.net/example.html',
            'is_published' => '1',
        ];

        $this->actingAs($lawyer)->post(route('admin.laws.store'), $payload)
            ->assertRedirect(route('admin.laws.index'));
        $law = Law::query()->where('slug', 'civil-code-of-the-philippines')->firstOrFail();
        $this->assertTrue($law->is_published);

        $this->put(route('admin.laws.update', $law), [...$payload, 'official_title' => 'Updated Civil Code Reference'])
            ->assertRedirect(route('admin.laws.index'));
        $this->assertDatabaseHas('laws', ['id' => $law->id, 'slug' => 'updated-civil-code-reference']);

        $this->delete(route('admin.laws.destroy', $law))->assertRedirect(route('admin.laws.index'));
        $this->assertDatabaseMissing('laws', ['id' => $law->id]);
    }
}
