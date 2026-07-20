<?php

namespace Feature;

use App\Enums\ArticleEnum;
use App\Enums\SettingEnum;
use App\Models\Article;
use App\Models\Provider;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProviderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organisations_are_visibly_listed()
    {
        $providers = Provider::factory(20)->create();
        $this->seed(SettingSeeder::class);
        $response = $this->get(route('home'));
        $response->assertSee(route('provider.index'));

        $response = $this->get(route('provider.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.provider.index');
        $providers = Provider::orderBy(
            'id',
            'DESC'
        )->paginate(SettingEnum::getValueByKeyword(SettingEnum::SETTINGS_PAGINATION_LENGTH));
        $response->assertViewHas(
            'providers',
            $providers,
        );

        $response->assertSee($providers->links());
    }

    #[Test]
    function single_page_of_organisation_can_be_viewed()
    {
        $provider = Provider::factory()->create();
        //setup

        //code
        $response = $this->get(route('provider.view', ['id' => $provider->id]));

        //assert
        $response->assertStatus(200);
        $response->assertViewIs('pages.provider.view');

        $response->assertViewHas(
            'provider',
            Provider::where(['id' => $provider->id]
            )->first(),
        );
    }

}
