<?php

namespace Tests\Unit;

use App\Enums\CulturalObjectEnum;
use App\Models\CulturalObject;
use App\Models\HasWebView;
use App\Models\Provider;
use App\Models\WebResource;
use App\Services\SearchService;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchServiceTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['cultural_object', 'provider', 'web_resource'] as $table) {
            DB::connection('secondary')->table($table)->truncate();
        }
    }

    #[Test]
    public function it_returns_all_objects_when_query_is_empty_or_whitespace()
    {
        $service = new SearchService();
        CulturalObject::factory(4)->create();

        $this->assertCount(4, $service->search('')->items());
        $this->assertCount(4, $service->search('   ')->items());
    }

    #[Test]
    public function it_finds_results_by_multiple_terms_using_and_logic()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create(['title' => 'Supplier A']);

        $object1 = CulturalObject::factory()->create([
            'title' => 'Red Dog Alpha',
            'description' => 'Document about stars.',
            'cultural_object_provided_by' => $provider1->id,
            'theme' => 'Astronomy',
        ]);

        $object2 = CulturalObject::factory()->create([
            'title' => 'Shadow of Dog',
            'description' => 'Some other data.',
            'cultural_object_provided_by' => $provider1->id,
            'theme' => 'Fiction',
        ]);

        $result = $service->search('Red Alpha');

        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->title, $result->items()[0]->title);
    }

    #[Test]
    public function it_handles_query_with_special_characters_and_normalization()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create([
            'title' => 'Item: Lost Key',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('Item:');
        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->title, $result->items()[0]->title);
    }

    #[Test]
    public function it_finds_results_by_exact_phrase_in_title()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        CulturalObject::factory()->create([
            'title' => 'The quick brown fox',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        CulturalObject::factory()->create([
            'title' => 'quick brown fox jumps over the lazy dog',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('"quick brown fox"');
        $this->assertCount(2, $result->items());
    }

    #[Test]
    public function it_does_not_find_result_for_non_matching_exact_phrase()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        CulturalObject::factory()->create([
            'title' => 'Alpha Bravo Charlie',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('"Bravo Alpha"');
        $this->assertCount(0, $result->items());
    }

    #[Test]
    public function it_finds_results_by_provider_title_using_id_lookup()
    {
        $service = new SearchService();

        $provider1 = Provider::factory()->create(['title' => 'Museum X']);
        $provider2 = Provider::factory()->create(['title' => 'Library Y']);

        CulturalObject::factory()->create(['title' => 'Obj 1', 'cultural_object_provided_by' => $provider1->id]);
        CulturalObject::factory()->create(['title' => 'Obj 2', 'cultural_object_provided_by' => $provider1->id]);
        CulturalObject::factory()->create(['title' => 'Obj 3', 'cultural_object_provided_by' => $provider2->id]);

        $result = $service->search('Museum');
        $this->assertCount(2, $result->items());

        $result = $service->search('Library');
        $this->assertCount(1, $result->items());
    }

    #[Test]
    public function it_finds_results_by_theme_field()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        CulturalObject::factory()->create(['title' => 'Obj 1', 'theme' => 'History']);
        CulturalObject::factory()->create(['title' => 'Obj 2', 'theme' => 'Geography']);
        CulturalObject::factory()->create(['title' => 'Obj 3', 'theme' => 'History']);

        $result = $service->search('History');
        $this->assertCount(2, $result->items());
    }

    #[Test]
    public function it_combines_title_and_theme_matches()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $term = 'Art';

        $object1 = CulturalObject::factory()->create([
            'title' => "Modern $term Work",
            'theme' => "Visual $term",
            'cultural_object_provided_by' => $provider1->id,
        ]);
        $object2 = CulturalObject::factory()->create([
            'title' => 'Sculpture Example',
            'theme' => "Visual $term",
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search($term);

        $this->assertEquals($object1->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_filters_based_on_multiple_terms_in_different_fields()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create([
            'title' => 'Old Boat',
            'description' => 'Report on blue pots.',
            'cultural_object_provided_by' => $provider1->id,
        ]);
        $object2 = CulturalObject::factory()->create([
            'title' => 'Old Tablet',
            'description' => '...',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('Old pots');

        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_prioritizes_by_score_over_creation_order()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $objectB = CulturalObject::factory()->create([
            'title' => 'Low Score',
            'description' => 'Contains the term KEYWORD.',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $objectA = CulturalObject::factory()->create([
            'title' => 'KEYWORD High Score',
            'description' => '...',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('KEYWORD');

        $this->assertCount(2, $result->items());
        $this->assertEquals($objectA->id, $result->items()[0]->id);
        $this->assertEquals($objectB->id, $result->items()[1]->id);
    }

    #[Test]
    public function it_sorts_by_score_based_on_field_weights_including_provider_lookup()
    {
        $service = new SearchService();

        $provider1 = Provider::factory()->create(['title' => 'Provider Source A']);
        $provider2 = Provider::factory()->create(['title' => 'Entity Beta']);

        $object1 = CulturalObject::factory()->create([
            'title' => 'Source Item A',
            'description' => 'A description with Source',
            'cultural_object_provided_by' => $provider2->id,
        ]);

        $object2 = CulturalObject::factory()->create([
            'title' => 'Item B',
            'description' => 'A description with Source',
            'cultural_object_provided_by' => $provider2->id,
        ]);

        $object3 = CulturalObject::factory()->create([
            'title' => 'Item C',
            'description' => 'Short text with Source',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('Source');

        $this->assertEquals(3, $result->count());
        $this->assertEquals($object1->id, $result->items()[0]->id);
        $this->assertEquals($object3->id, $result->items()[1]->id);
        $this->assertEquals($object2->id, $result->items()[2]->id);
    }

    #[Test]
    public function it_prioritizes_exact_uppercase_title_match()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create(['title' => 'PROJECT HYDRAULICS', 'cultural_object_provided_by' => $provider1->id]);
        $object2 = CulturalObject::factory()->create(['title' => 'HYDRAULICS', 'cultural_object_provided_by' => $provider1->id]);
        $object3 = CulturalObject::factory()->create(['title' => 'hydraulics', 'cultural_object_provided_by' => $provider1->id]);

        $result = $service->search('HYDRAULICS');

        $this->assertEquals($object2->id, $result->items()[0]->id);

        $object4 = CulturalObject::factory()->create(['title' => 'ARTIFACT-5', 'cultural_object_provided_by' => $provider1->id]);
        $result = $service->search('ARTIFACT-5');
        $this->assertEquals($object4->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_uses_id_as_secondary_sort_for_equal_scores()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $objectA = CulturalObject::factory()->create(['title' => 'Equal Score Term XYZ', 'cultural_object_provided_by' => $provider1->id]);
        $objectB = CulturalObject::factory()->create(['title' => 'Equal Score Term XYZ', 'cultural_object_provided_by' => $provider1->id]);

        $result = $service->search('XYZ');

        $this->assertCount(2, $result->items());
        $this->assertEquals($objectA->id, $result->items()[0]->id);
        $this->assertEquals($objectB->id, $result->items()[1]->id);
    }

    #[Test]
    public function it_prioritizes_by_score_based_on_explicit_weights()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();
        $term = 'SPECIALTERM';

        $object2 = CulturalObject::factory()->create([
            'title' => 'The ' . $term . ' Object',
            'description' => 'A long description.',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $object3 = CulturalObject::factory()->create([
            'title' => 'Just another object',
            'description' => 'This description contains ' . $term . '.',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $provider2 = Provider::factory()->create(['title' => 'Provider With ' . $term]);
        $object1 = CulturalObject::factory()->create([
            'title' => 'Item X',
            'description' => '...',
            'cultural_object_provided_by' => $provider2->id,
        ]);

        $result = $service->search($term);

        $this->assertEquals($object2->id, $result->items()[0]->id);
        $this->assertEquals($object3->id, $result->items()[1]->id);
        $this->assertEquals($object1->id, $result->items()[2]->id);
    }

    #[Test]
    public function it_requires_all_terms_to_match_in_at_least_one_field_to_be_included()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create([
            'title' => 'Apple Pie Recipe',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        CulturalObject::factory()->create([
            'title' => 'Apple Tree',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $object3 = CulturalObject::factory()->create([
            'title' => 'Pie Crust',
            'description' => 'A description about the Apple.',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('Apple Pie');

        $this->assertCount(2, $result->items());
        $this->assertContains($object1->id, collect($result->items())->pluck('id')->toArray());
        $this->assertContains($object3->id, collect($result->items())->pluck('id')->toArray());
    }

    #[Test]
    public function it_filters_out_results_when_a_term_is_not_present_in_any_weighted_field()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        CulturalObject::factory()->create([
            'title' => 'Ancient Greek Sculpture',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        CulturalObject::factory()->create([
            'title' => 'Roman Sculpture',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result = $service->search('Ancient Sculpture');

        $this->assertCount(1, $result->items());
        $this->assertEquals('Ancient Greek Sculpture', $result->items()[0]->title);
    }

    #[Test]
    public function it_removes_duplicate_results_from_join_with_provider()
    {
        $service = new SearchService();

        $provider1 = Provider::factory()->create(['title' => 'Provider A']);
        $provider2 = Provider::factory()->create(['title' => 'Provider B']);

        $object1 = CulturalObject::factory()->create([
            'title' => 'Unique Artifact',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $object2 = CulturalObject::factory()->create([
            'title' => 'Unique Artifact',
            'cultural_object_provided_by' => $provider2->id,
        ]);

        $results = $service->search('Unique Artifact');

        $this->assertEquals(2, collect($results->items())->unique('id')->count());
    }

    #[Test]
    public function it_is_case_insensitive_and_finds_match_regardless_of_case()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create([
            'title' => 'Ancient VASE from Rome',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        $result_lower = $service->search('ancient vase');
        $this->assertCount(1, $result_lower->items());
        $this->assertEquals($object1->title, $result_lower->items()[0]->title);

        $result_mixed = $service->search('AnCiEnT VaSe');
        $this->assertCount(1, $result_mixed->items());
    }

    #[Test]
    public function it_applies_pagination_limit_correctly_with_2_limit()
    {
        $service = new SearchService();

        CulturalObject::factory(2)->create(['title' => 'Test Item 1000']);

        $result = $service->search('Test Item 1000',1);


        $this->assertCount(1, $result->items());

        $this->assertEquals(2, $result->total());
        $this->assertEquals(1, $result->currentPage());
    }

    #[Test]
    public function it_filters_by_contains_mode_on_title()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create([
            'title' => 'Medieval Sword',
            'cultural_object_provided_by' => $provider->id,
        ]);

        CulturalObject::factory()->create([
            'title' => 'Ancient Vase',
            'cultural_object_provided_by' => $provider->id,
        ]);

        $filters = [
            'title' => ['value' => 'Sword', 'mode' => '1'],
        ];

        $result = $service->search('', 10, $filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_filters_by_not_contains_mode_on_title()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create(['title' => 'Medieval Sword', 'cultural_object_provided_by' => $provider->id]);
        CulturalObject::factory()->create(['title' => 'Ancient Vase', 'cultural_object_provided_by' => $provider->id]);

        $filters = [
            'title' => ['value' => 'Sword', 'mode' => '0'],
        ];

        $result = $service->search('', 10, $filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals('Ancient Vase', $result->items()[0]->title);
    }

    #[Test]
    public function it_filters_by_provider_field()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create(['title' => 'National Museum']);
        $provider2 = Provider::factory()->create(['title' => 'City Library']);

        $object1 = CulturalObject::factory()->create([
            'title' => 'Old Manuscript',
            'cultural_object_provided_by' => $provider1->id,
        ]);

        CulturalObject::factory()->create([
            'title' => 'Rare Book',
            'cultural_object_provided_by' => $provider2->id,
        ]);

        $filters = [
            'provider' => ['value' => 'Museum', 'mode' => '1'],
        ];

        $result = $service->search('', 10, $filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_combines_multiple_filters_with_and_logic()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create([
            'title' => 'Medieval Sword',
            'description' => 'Artifact from the 14th century',
            'theme' => 'History',
            'cultural_object_provided_by' => $provider->id,
        ]);

        CulturalObject::factory()->create([
            'title' => 'Ancient Vase',
            'description' => 'Greek artifact',
            'theme' => 'Archaeology',
            'cultural_object_provided_by' => $provider->id,
        ]);

        $filters = [
            'description' => ['value' => '14th century', 'mode' => '1'],
            'theme' => ['value' => 'History', 'mode' => '1'],
        ];

        $result = $service->search('', 10, $filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_returns_all_objects_if_filters_are_empty()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();
        CulturalObject::factory(3)->create(['cultural_object_provided_by' => $provider->id]);

        $filters = [];

        $result = $service->search('', 10, $filters);

        $this->assertEquals(3, $result->total());
    }

    #[Test]
    public function it_ignores_unknown_fields_in_filters()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();
        $object = CulturalObject::factory()->create(['title' => 'Ancient Vase', 'cultural_object_provided_by' => $provider->id]);

        $filters = [
            'unknown_field' => ['value' => 'test', 'mode' => '1'],
        ];

        $result = $service->search('', 10, $filters);

        $this->assertEquals(1, $result->total());
        $this->assertEquals($object->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_ignores_empty_filter_values()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create(['title' => 'Sword', 'cultural_object_provided_by' => $provider->id]);

        $filters = [
            'title' => ['value' => '', 'mode' => '1'],
        ];

        $result = $service->search('', 10, $filters);
        $this->assertEquals(1, $result->total());
    }

    #[Test]
    public function it_applies_multiple_filters_with_mixed_modes()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        CulturalObject::factory()->create(['title' => 'Ancient Sword', 'theme' => 'History', 'cultural_object_provided_by' => $provider->id]);
        $object2 = CulturalObject::factory()->create(['title' => 'Medieval Shield', 'theme' => 'History', 'cultural_object_provided_by' => $provider->id]);

        $filters = [
            'title' => ['value' => 'Sword', 'mode' => '0'],
            'theme' => ['value' => 'History', 'mode' => '1'],
        ];

        $result = $service->search('', 10, $filters);
        $this->assertCount(1, $result->items());
        $this->assertEquals($object2->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_filters_by_not_contains_excluding_match()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        CulturalObject::factory()->create(['title' => 'Title 1', 'description' => 'Blue Painting', 'cultural_object_provided_by' => $provider->id]);

        $object2 = CulturalObject::factory()->create(['title' => 'Title 2', 'description' => 'Red Sculpture', 'cultural_object_provided_by' => $provider->id]);

        $object3 = CulturalObject::factory()->create(['title' => 'Title 3', 'description' => 'Yellow Object', 'cultural_object_provided_by' => $provider->id]);

        $filters = [
            'description' => ['value' => 'Blue', 'mode' => '0'],
        ];

        $result = $service->search('', 10, $filters);
        $resultCollection = collect($result->items());

        $this->assertCount(2, $result->items());
        $this->assertTrue($resultCollection->pluck('id')->contains($object2->id));
        $this->assertTrue($resultCollection->pluck('id')->contains($object3->id));
    }

    #[Test]
    public function it_filters_by_not_contains_on_date_field()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        CulturalObject::factory()->create(['title' => 'Obj 1', 'creation_date' => '2023-01-15', 'cultural_object_provided_by' => $provider->id]);

        $object2 = CulturalObject::factory()->create(['title' => 'Obj 2', 'creation_date' => '2022-12-01', 'cultural_object_provided_by' => $provider->id]);

        $object3 = CulturalObject::factory()->create(['title' => 'Obj 3', 'creation_date' => '2022-11-20', 'cultural_object_provided_by' => $provider->id]);

        $filters = [
            'creation_date' => ['value' => '2023', 'mode' => '0'],
        ];

        $result = $service->search('', 10, $filters);
        $resultCollection = collect($result->items());

        $this->assertCount(2, $result->items());
        $this->assertTrue($resultCollection->pluck('id')->contains($object2->id));
        $this->assertTrue($resultCollection->pluck('id')->contains($object3->id));
    }


    #[Test]
    public function it_filters_by_not_contains_on_numeric_field_like_description()
    {
        $service = new SearchService();
        $provider = Provider::factory()->create();

        CulturalObject::factory()->create(['description' => 'Artifact 123', 'cultural_object_provided_by' => $provider->id]);

        $object2 = CulturalObject::factory()->create(['description' => 'Artifact 456', 'cultural_object_provided_by' => $provider->id]);

        $filters = [
            'description' => ['value' => '123', 'mode' => '0'],
        ];

        $result = $service->search('', 10, $filters);

        $this->assertCount(1, $result->items());
        $this->assertEquals($object2->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_filters_by_single_quick_filter_value()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create(['title' => 'Obj 1', 'theme' => 'History']);
        $object2 = CulturalObject::factory()->create(['title' => 'Obj 2', 'theme' => 'Geography']);
        $object3 = CulturalObject::factory()->create(['title' => 'Obj 3', 'theme' => 'History']);

        $quickFilters = ['theme' => 'History'];

        $result = $service->search('', 10, [], $quickFilters);

        $this->assertCount(2, $result->items());
        $resultIds = collect($result->items())->pluck('id');
        $this->assertTrue($resultIds->contains($object1->id));
        $this->assertTrue($resultIds->contains($object3->id));
        $this->assertFalse($resultIds->contains($object2->id));
    }

    #[Test]
    public function it_filters_by_multiple_quick_filter_values_in_one_field()
    {
        $service = new SearchService();

        $webResource1 = WebResource::factory()->create(['visualizationtype' => 'image']);
        $webResource2 = WebResource::factory()->create(['visualizationtype' => 'audio']);
        $webResource3 = WebResource::factory()->create(['visualizationtype' => 'video']);

        $object1 = CulturalObject::factory()->create(['title' => 'Obj 1']);
        $object2 = CulturalObject::factory()->create(['title' => 'Obj 2']);
        $object3 = CulturalObject::factory()->create(['title' => 'Obj 3']);

        HasWebView::factory()->create([
            'cultural_object_id' => $object1->id,
            'web_resource_id' => $webResource1->id,
        ]);

        HasWebView::factory()->create([
            'cultural_object_id' => $object2->id,
            'web_resource_id' => $webResource2->id,
        ]);

        HasWebView::factory()->create([
            'cultural_object_id' => $object3->id,
            'web_resource_id' => $webResource3->id,
        ]);

        $quickFilters = ['type' => ['image', 'audio']];

        $result = $service->search('', 10, [], $quickFilters);

        $this->assertCount(2, $result->items());

        $ids = collect($result->items())->pluck('id');

        $this->assertTrue($ids->contains($object1->id));
        $this->assertTrue($ids->contains($object2->id));
        $this->assertTrue($ids->doesntContain($object3->id));
    }

    #[Test]
    public function it_filters_by_multiple_quick_filter_values_passed_as_comma_separated_string()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create(['title' => 'Obj 1', 'theme' => 'Art']);
        $object2 = CulturalObject::factory()->create(['title' => 'Obj 2', 'theme' => 'Science']);

        $quickFilters = ['theme' => 'Art,Science'];

        $result = $service->search('', 10, [], $quickFilters);

        $this->assertCount(2, $result->items());
    }

    #[Test]
    public function it_filters_by_multiple_quick_filters_in_different_fields_using_and_logic()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $webResource1 = WebResource::factory()->create(['visualizationtype' => 'photo']);
        $webResource2 = WebResource::factory()->create(['visualizationtype' => 'video']);
        $webResource3 = WebResource::factory()->create(['visualizationtype' => 'photo']);

        $object1 = CulturalObject::factory()->create(['title' => 'Match', 'theme' => 'Art']);
        $object2 = CulturalObject::factory()->create(['title' => 'No Match', 'theme' => 'Art']);
        $object3 = CulturalObject::factory()->create(['title' => 'No Match', 'theme' => 'Science']);

        HasWebView::factory()->create([
            'cultural_object_id' => $object1->id,
            'web_resource_id' => $webResource1->id,
        ]);

        HasWebView::factory()->create([
            'cultural_object_id' => $object2->id,
            'web_resource_id' => $webResource2->id,
        ]);

        HasWebView::factory()->create([
            'cultural_object_id' => $object3->id,
            'web_resource_id' => $webResource3->id,
        ]);

        $quickFilters = [
            'theme' => 'Art',
            'type' => ['photo'],
        ];

        $result = $service->search('', 10, [], $quickFilters);

        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_filters_by_date_range()
    {
        $service = new SearchService();

        $object1 = CulturalObject::factory()->create(['title' => 'Old', 'creation_date' => '1990-05-10']);
        $object2 = CulturalObject::factory()->create(['title' => 'Recent', 'creation_date' => '2010-01-01']);
        $object3 = CulturalObject::factory()->create(['title' => 'New', 'creation_date' => '2020-12-31']);

        $quickFilters = [
            'date_from' => '2000-01-01',
            'date_to' => '2025-01-01',
        ];

        $result = $service->search('', 10, [], $quickFilters);

        $this->assertCount(2, $result->items());
        $resultIds = collect($result->items())->pluck('id');
        $this->assertFalse($resultIds->contains($object1->id));
        $this->assertTrue($resultIds->contains($object2->id));
        $this->assertTrue($resultIds->contains($object3->id));
    }

    #[Test]
    public function it_combines_text_query_with_quick_filters()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $object1 = CulturalObject::factory()->create(['title' => 'Red Car', 'theme' => 'Vehicles']);
        $object2 = CulturalObject::factory()->create(['title' => 'Red Boat', 'theme' => 'Vehicles']);
        $object3 = CulturalObject::factory()->create(['title' => 'Blue Car', 'theme' => 'Animals']);

        $quickFilters = ['theme' => 'Vehicles'];
        $query = 'Red';

        $result = $service->search($query, 10, [], $quickFilters);

        $this->assertCount(2, $result->items());
        $resultIds = collect($result->items())->pluck('id');
        $this->assertTrue($resultIds->contains($object1->id));
        $this->assertTrue($resultIds->contains($object2->id));
        $this->assertFalse($resultIds->contains($object3->id));
    }

    #[Test]
    public function it_combines_advanced_filters_with_quick_filters()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create(['title' => 'Museum A']);
        $provider2 = Provider::factory()->create(['title' => 'Museum B']);

        $object1 = CulturalObject::factory()->create(['title' => 'Medieval Sword', 'theme' => 'History', 'cultural_object_provided_by' => $provider1->id]);
        $object2 = CulturalObject::factory()->create(['title' => 'Ancient Vase', 'theme' => 'History', 'cultural_object_provided_by' => $provider2->id]);
        $object3 = CulturalObject::factory()->create(['title' => 'Silver Coin', 'theme' => 'Archaeology', 'cultural_object_provided_by' => $provider1->id]);

        $advancedFilters = [
            'title' => ['value' => 'Med', 'mode' => '1'],
        ];

        $quickFilters = [
            'theme' => 'History',
        ];

        $result = $service->search('', 10, $advancedFilters, $quickFilters);

        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->id, $result->items()[0]->id);
    }


    #[Test]
    public function it_calculates_facets_based_on_advanced_filters()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create(['title' => 'Provider A']);
        $provider2 = Provider::factory()->create(['title' => 'Provider B']);

        $object1 = CulturalObject::factory()->create(['title' => 'Blue Book', 'theme' => 'Art', 'type' => 'Text', 'cultural_object_provided_by' => $provider1->id]);

        $object2 = CulturalObject::factory()->create(['title' => 'Red Car', 'theme' => 'Vehicles', 'type' => 'Photo', 'cultural_object_provided_by' => $provider2->id]);

        $advancedFilters = [
            'title' => ['value' => 'Book', 'mode' => '1'],
        ];

        $facets = $service->getFacets($advancedFilters, []);

        $this->assertCount(1, $facets['theme']);
        $this->assertContains('Art', $facets['theme']);
        $this->assertNotContains('Vehicles', $facets['theme']);

        $this->assertCount(1, $facets['provider']);
        $this->assertContains('Provider A', $facets['provider']);
    }

    #[Test]
    public function it_calculates_facets_excluding_the_current_quick_filter_field()
    {
        $service = new SearchService();
        $provider1 = Provider::factory()->create();

        $webResource1 = WebResource::factory()->create(['visualizationtype' => 'image']);
        $webResource2 = WebResource::factory()->create(['visualizationtype' => 'video']);

        $object1 = CulturalObject::factory()->create(['title' => 'Obj 1', 'theme' => 'Art', 'cultural_object_provided_by' => $provider1->id]);
        $object2 = CulturalObject::factory()->create(['title' => 'Obj 2', 'theme' => 'Science', 'cultural_object_provided_by' => $provider1->id]);

        HasWebView::factory()->create([
            'cultural_object_id' => $object1->id,
            'web_resource_id' => $webResource1->id,
        ]);

        HasWebView::factory()->create([
            'cultural_object_id' => $object2->id,
            'web_resource_id' => $webResource2->id,
        ]);

        $quickFilters = ['type' => 'image'];

        $facets = $service->getFacets([], $quickFilters);

        $this->assertCount(1, $facets['theme']);
        $this->assertContains('Art', $facets['theme']);
        $this->assertNotContains('Science', $facets['theme']);

        $this->assertCount(2, $facets['type']);

        $readableTypes = array_values(array_unique(array_map(
            fn($type) => CulturalObjectEnum::getReadableVisualisation($type) ?? $type,
            ['image', 'video']
        )));

        foreach ($readableTypes as $readableType) {
            $this->assertContains($readableType, $facets['type']);
        }
    }

    #[Test]
    public function it_calculates_facets_correctly_when_excluding_current_filter_with_other_filters_present()
    {
        $service = new SearchService();

        $webResourceImage = WebResource::factory()->create(['visualizationtype' => 'image']);
        $webResourceVideo = WebResource::factory()->create(['visualizationtype' => 'video']);

        $object1 = CulturalObject::factory()->create(['theme' => 'Art']);
        HasWebView::factory()->create(['cultural_object_id' => $object1->id, 'web_resource_id' => $webResourceImage->id]);

        $object2 = CulturalObject::factory()->create(['theme' => 'Art']);
        HasWebView::factory()->create(['cultural_object_id' => $object2->id, 'web_resource_id' => $webResourceVideo->id]);

        $object3 = CulturalObject::factory()->create(['theme' => 'Science']);
        HasWebView::factory()->create(['cultural_object_id' => $object3->id, 'web_resource_id' => $webResourceImage->id]);

        $object4 = CulturalObject::factory()->create(['theme' => 'Science']);
        HasWebView::factory()->create(['cultural_object_id' => $object4->id, 'web_resource_id' => $webResourceVideo->id]);


        $advancedFilters = [
            'theme' => [
                'value' => 'Art',
                'mode' => '1'
            ]
        ];

        $quickFilters = [
            'type' => CulturalObjectEnum::getReadableVisualisation('image'),
        ];

        $facets = $service->getFacets($advancedFilters, $quickFilters);

        $readableImage = CulturalObjectEnum::getReadableVisualisation('image');
        $readableVideo = CulturalObjectEnum::getReadableVisualisation('video');

        $this->assertCount(1, $facets['theme']);
        $this->assertContains('Art', $facets['theme']);
        $this->assertNotContains('Science', $facets['theme']);

        $this->assertCount(2, $facets['type']);
        $this->assertContains($readableImage, $facets['type']);
        $this->assertContains($readableVideo, $facets['type']);

    }

    #[Test]
    public function it_filters_by_date_from_only()
    {
        $service = new SearchService();

        $object1 = CulturalObject::factory()->create(['title' => 'Old', 'creation_date' => '1990-05-10']);
        $object2 = CulturalObject::factory()->create(['title' => 'Recent', 'creation_date' => '2010-01-01']);

        $quickFilters = [
            'date_from' => '2000-01-01',
        ];

        $result = $service->search('', 10, [], $quickFilters);
        $this->assertCount(1, $result->items());
        $this->assertEquals($object2->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_filters_by_date_to_only()
    {
        $service = new SearchService();

        $object1 = CulturalObject::factory()->create(['title' => 'Old', 'creation_date' => '1990-05-10']);
        $object2 = CulturalObject::factory()->create(['title' => 'Recent', 'creation_date' => '2010-01-01']);

        $quickFilters = [
            'date_to' => '2000-01-01',
        ];

        $result = $service->search('', 10, [], $quickFilters);
        $this->assertCount(1, $result->items());
        $this->assertEquals($object1->id, $result->items()[0]->id);
    }

    #[Test]
    public function it_excludes_results_with_advanced_filter_mode_zero()
    {
        $service = new SearchService();

        $object1 = CulturalObject::factory()->create(['title' => 'Red Car', 'theme' => 'Vehicles']);
        $object2 = CulturalObject::factory()->create(['title' => 'Blue Car', 'theme' => 'Vehicles']);
        $object3 = CulturalObject::factory()->create(['title' => 'Green Car', 'theme' => 'Vehicles']);

        $advancedFilters = [
            'title' => ['value' => 'Red', 'mode' => '0'],
        ];

        $result = $service->search('', 10, $advancedFilters);

        $resultIds = collect($result->items())->pluck('id');
        $this->assertFalse($resultIds->contains($object1->id));
        $this->assertTrue($resultIds->contains($object2->id));
        $this->assertTrue($resultIds->contains($object3->id));
    }

    #[Test]
    public function it_returns_all_items_when_quick_filters_are_empty()
    {
        $service = new SearchService();

        $object1 = CulturalObject::factory()->create(['title' => 'Obj 1']);
        $object2 = CulturalObject::factory()->create(['title' => 'Obj 2']);

        $quickFilters = [];

        $result = $service->search('', 10, [], $quickFilters);

        $this->assertCount(2, $result->items());
    }


    #[Test]
    public function it_correctly_handles_empty_and_null_quick_filter_values()
    {
        $service = new SearchService();

        $object1 = CulturalObject::factory()->create(['theme' => 'Art', 'type' => 'Photo']);
        $object2 = CulturalObject::factory()->create(['theme' => 'Science', 'type' => 'Video']);

        $quickFilters = [
            'theme' => '',
            'type' => null,
        ];

        $result = $service->search('', 10, [], $quickFilters);

        $this->assertCount(2, $result->items());
    }
    #[Test]
    public function it_throws_exception_when_no_results_found_for_export()
    {
        $serviceMock = $this->partialMock(SearchService::class, function ($mock) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('search')
                ->once()
                ->andReturn(
                    (new LengthAwarePaginator(
                        collect(),
                        0,
                        1000,
                        1
                    ))
                        ->setCollection(collect())
                );
        });

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(__('search.no_results_export'));

        $serviceMock->prepareCsvExport('query', [], []);
    }

    #[Test]
    public function it_generates_csv_with_correct_headers_from_casts()
    {
        $object = CulturalObject::factory()->makeOne();
        $items = collect([$object]);

        $serviceMock = $this->partialMock(SearchService::class, function ($mock) use ($items) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('search')
                ->once()
                ->andReturn(
                    (new LengthAwarePaginator(
                        $items,
                        $items->count(),
                        1000,
                        1
                    ))
                        ->setCollection($items)
                );
        });

        $csvCallback = $serviceMock->prepareCsvExport('query', [], []);

        ob_start();
        $csvCallback();
        $csvContent = ob_get_clean();

        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csvContent);
        rewind($handle);

        if (substr($csvContent, 0, 3) === chr(0xEF).chr(0xBB).chr(0xBF)) {
            fseek($handle, 3);
        }

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = $data;
        }
        fclose($handle);

        $this->assertGreaterThan(0, count($rows));

        $expectedHeaders = array_keys((new CulturalObject())->casts);
        $this->assertEquals($expectedHeaders, $rows[0]);
    }

    #[Test]
    public function it_exports_data_rows_correctly_and_handles_nulls()
    {
        $objectA = CulturalObject::factory()->makeOne([
            'id' => 1,
            'title' => 'Title A',
            'description' => 'Description for A',
            'amount' => 15.50,
        ]);

        $objectB = CulturalObject::factory()->makeOne([
            'id' => 2,
            'title' => 'Title B',
            'description' => null,
            'amount' => 99.99,
        ]);

        $items = collect([$objectA, $objectB]);
        $expectedHeaders = array_keys((new CulturalObject())->casts);

        $serviceMock = $this->partialMock(SearchService::class, function ($mock) use ($items) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('search')
                ->once()
                ->andReturn(
                    (new LengthAwarePaginator(
                        $items,
                        $items->count(),
                        1000,
                        1
                    ))
                        ->setCollection($items)
                );
        });

        $csvCallback = $serviceMock->prepareCsvExport('query', [], []);

        ob_start();
        $csvCallback();
        $csvContent = ob_get_clean();

        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csvContent);
        rewind($handle);

        if (substr($csvContent, 0, 3) === chr(0xEF).chr(0xBB).chr(0xBF)) {
            fseek($handle, 3);
        }

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = $data;
        }
        fclose($handle);

        $this->assertCount(3, $rows);

        $rowDataA = array_combine($expectedHeaders, $rows[1]);
        $this->assertEquals('Title A', $rowDataA['title']);
        $this->assertEquals('Description for A', $rowDataA['description']);
        $this->assertEquals('1', $rowDataA['id']);

        $rowDataB = array_combine($expectedHeaders, $rows[2]);
        $this->assertEquals('Title B', $rowDataB['title']);
        $this->assertEquals('', $rowDataB['description']);
        $this->assertEquals('2', $rowDataB['id']);
    }

    #[Test]
    public function it_includes_utf8_bom()
    {
        $object = CulturalObject::factory()->makeOne();
        $items = collect([$object]);

        $serviceMock = $this->partialMock(SearchService::class, function ($mock) use ($items) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('search')
                ->once()
                ->andReturn(
                    (new LengthAwarePaginator($items, $items->count(), 1000, 1))
                        ->setCollection($items)
                );
        });

        $csvCallback = $serviceMock->prepareCsvExport('query', [], []);

        ob_start();
        $csvCallback();
        $csvContent = ob_get_clean();

        $this->assertStringStartsWith(chr(0xEF).chr(0xBB).chr(0xBF), $csvContent);
    }

    #[Test]
    public function it_handles_large_number_of_records()
    {
        $items = CulturalObject::factory()->count(1000)->make();

        $serviceMock = $this->partialMock(SearchService::class, function ($mock) use ($items) {
            $mock->shouldAllowMockingProtectedMethods()
                ->shouldReceive('search')
                ->once()
                ->andReturn(
                    (new LengthAwarePaginator($items, $items->count(), 1000, 1))
                        ->setCollection($items)
                );
        });

        $csvCallback = $serviceMock->prepareCsvExport('query', [], []);

        ob_start();
        $csvCallback();
        $csvContent = ob_get_clean();

        $rows = explode("\n", trim($csvContent));

        $this->assertCount(1001, $rows);
    }
}
