<?php

namespace Tests\Livewire;

use App\Livewire\RegisterForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_register_form_component()
    {
        Livewire::test(RegisterForm::class)
            ->assertSee('first_name')
            ->assertSee('last_name')
            ->assertSee('email')
            ->assertSee('password');
    }

    #[Test]
    public function it_validates_first_name_field()
    {
        Livewire::test(RegisterForm::class)
            ->set('first_name', '')
            ->call('register')
            ->assertHasErrors(['first_name' => 'required'])
            ->set('first_name', str_repeat('a', 51))
            ->call('register')
            ->assertHasErrors(['first_name' => 'max']);
    }

    #[Test]
    public function it_validates_last_name_field()
    {
        Livewire::test(RegisterForm::class)
            ->set('last_name', '')
            ->call('register')
            ->assertHasErrors(['last_name' => 'required'])
            ->set('last_name', str_repeat('a', 51))
            ->call('register')
            ->assertHasErrors(['last_name' => 'max']);
    }

    #[Test]
    public function it_validates_email_field()
    {
        Livewire::test(RegisterForm::class)
            ->set('email', '')
            ->call('register')
            ->assertHasErrors(['email' => 'required'])
            ->set('email', 'invalid-email')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    public function it_validates_unique_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(RegisterForm::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'existing@example.com')
            ->set('password', 'Strong123!')
            ->set('password_confirmation', 'Strong123!')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    #[Test]
    public function it_validates_password_strength()
    {
        Livewire::test(RegisterForm::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'weak')
            ->set('password_confirmation', 'weak')
            ->call('register')
            ->assertHasErrors(['password'])
            ->set('password', 'Strong123!')
            ->set('password_confirmation', 'Strong123!')
            ->call('register')
            ->assertHasNoErrors(['password']);
    }

    #[Test]
    public function it_validates_password_confirmation()
    {
        Livewire::test(RegisterForm::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'Strong123!')
            ->set('password_confirmation', 'Different123!')
            ->call('register')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    /** @deprecated  */
    public function it_validates_profile_image_with_text_file()
    {
        Storage::fake('public');


        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        Livewire::test(RegisterForm::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'Strong123!')
            ->set('password_confirmation', 'Strong123!')
            ->set('profile_image', $invalidFile)
            ->call('register')
            ->assertHasErrors(['profile_image' => 'image']);
    }

    /** @deprecated  */
    public function it_validates_profile_image_size()
    {
        Storage::fake('public');

        // Създаване на текстов файл с голям размер
        $largeFile = UploadedFile::fake()->create('large.jpg', 3000);

        Livewire::test(RegisterForm::class)
            ->set('first_name', 'Test')
            ->set('last_name', 'User')
            ->set('email', 'test@example.com')
            ->set('password', 'Strong123!')
            ->set('password_confirmation', 'Strong123!')
            ->set('profile_image', $largeFile)
            ->call('register')
            ->assertHasErrors(['profile_image' => 'max']);
    }

    #[Test]
    public function it_calculates_password_strength_correctly()
    {
        $component = Livewire::test(RegisterForm::class);

        $strength = $component->set('password', '')->instance()->passwordStrength();
        $this->assertSame('слаба', $strength);

        $strength = $component->set('password', 'short')->instance()->passwordStrength();
        $this->assertSame('слаба', $strength);


        $strength = $component->set('password', 'LongerPassword')->instance()->passwordStrength();
        $this->assertSame('нормална', $strength);

        $strength = $component->set('password', 'Strong123!')->instance()->passwordStrength();
        $this->assertSame('силна', $strength);
    }

    #[Test]
    public function it_validates_fields_on_update()
    {
        Livewire::test(RegisterForm::class)
            ->set('first_name', '')
            ->assertHasErrors(['first_name' => 'required'])
            ->set('email', 'invalid')
            ->assertHasErrors(['email' => 'email'])
            ->set('password', 'weak')
            ->assertHasErrors(['password']);
    }
}
