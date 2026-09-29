<?php

use App\Models\User;

test('guests can view the landing page', function () {
    $response = $this->get(route('welcome'));

    $response->assertOk();
    $response->assertSee('Trade what you know for what you need');
    $response->assertSee(route('register'));
    $response->assertSee(route('login'));
});

test('authenticated users are redirected from the landing page to the home page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('welcome'));

    $response->assertRedirect(route('home'));
});
