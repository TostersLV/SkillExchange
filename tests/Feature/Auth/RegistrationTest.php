<?php

use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    // Nosūta reģistrācijas formas datus ar derīgu lietotājvārdu, e-pastu un paroli
    $response = $this->post(route('register.store'), [
        'username' => 'JohnDoe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Kļūdu nav, un lietotājs tiek pārvirzīts uz sākumlapu
    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('home', absolute: false));

    // Lietotājs ir pieteicies sistēmā, un profils ir saglabāts datubāzē
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['username' => 'JohnDoe', 'email' => 'test@example.com']);
});

test('registration is refused for invalid input', function (array $changes, string $field) {
    // Derīgi dati, no kuriem katrā gadījumā tiek mainīts viens lauks
    $input = array_merge([
        'username' => 'JohnDoe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $changes);

    // Sistēma atgriež kļūdu konkrētajam laukam
    $this->post(route('register.store'), $input)->assertSessionHasErrors($field);

    // Lietotājs nav pieteicies, un profils netiek izveidots
    $this->assertGuest();
    expect(User::count())->toBe(0);
})->with([
    'username too short' => [['username' => 'abc'], 'username'],
    'reserved deleted user name' => [['username' => 'Deleted user'], 'username'],
    'reserved name in other case and spacing' => [['username' => ' deleted   USER '], 'username'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'password too short' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'passwords do not match' => [['password_confirmation' => 'different'], 'password'],
]);

test('registration is refused when the username or email is already taken', function (string $field) {
    // Datubāzē jau ir lietotājs ar šādu lietotājvārdu un e-pastu
    User::factory()->create(['username' => 'TakenName', 'email' => 'taken@example.com']);

    // Jaunais lietotājs atkārto vienu no aizņemtajām vērtībām
    $input = array_merge([
        'username' => 'FreshName',
        'email' => 'fresh@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], [$field => $field === 'username' ? 'TakenName' : 'taken@example.com']);

    $this->post(route('register.store'), $input)->assertSessionHasErrors($field);

    // Jaunais profils netiek izveidots, datubāzē paliek tikai oriģinālais lietotājs
    expect(User::count())->toBe(1);
})->with(['username', 'email']);
