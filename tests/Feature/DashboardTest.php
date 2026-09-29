<?php

use App\Models\User;

test('guests visiting the home page are redirected to the landing page', function () {
    $response = $this->get(route('home'));
    $response->assertRedirect(route('welcome'));
});

test('guests visiting other protected pages are redirected to the login page', function () {
    $response = $this->get(route('posts.create'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the home page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));
    $response->assertOk();
});
