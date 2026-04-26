<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_redirects_guests_to_login_from_root(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));
    }
}
