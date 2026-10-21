<?php

use App\Livewire\Settings\Profile;
use App\Models\Message;
use App\Models\Post;
use App\Models\PostOffer;
use App\Models\User;
use App\PostOfferStatus;
use App\PostStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    // Pieteicies lietotājs atver profila iestatījumu komponenti
    $user = User::factory()->create();

    $this->actingAs($user);

    // Ievada jaunu lietotājvārdu un e-pastu un saglabā izmaiņas
    $response = Livewire::test(Profile::class)
        ->set('username', 'TestUser')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    // Nav validācijas kļūdu
    $response->assertHasNoErrors();

    $user->refresh();

    // Jaunie dati ir saglabāti datubāzē
    expect($user->username)->toEqual('TestUser');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(Profile::class)
        ->set('username', 'TestUser')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('deleting an account erases personal details and the old login stops working', function () {
    $user = User::factory()->create(['bio' => 'I teach chess.']);
    $originalEmail = $user->email;

    $this->actingAs($user);

    Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect(auth()->check())->toBeFalse();

    $user->refresh();
    expect($user->username)->toBe('Deleted user');
    expect($user->email)->not->toBe($originalEmail);
    expect($user->bio)->toBeNull();

    $this->post(route('login.store'), ['email' => $originalEmail, 'password' => 'password']);
    $this->assertGuest();
});

test('a user with an exchange in progress cannot delete their account', function () {
    $offer = PostOffer::factory()->accepted()->create();
    $originalEmail = $offer->user->email;

    $this->actingAs($offer->user);

    Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasErrors(['password']);

    expect($offer->user->fresh()->email)->toBe($originalEmail);
});

test('deleting an account keeps finished exchanges and chats for the other member', function () {
    $offer = PostOffer::factory()->for(Post::factory()->state(['status' => PostStatus::COMPLETED]))->create(['status' => PostOfferStatus::ACCEPTED]);
    $message = Message::factory()->for($offer, 'postOffer')->for($offer->user)->create(['body' => 'Thanks for the lesson!']);

    $this->actingAs($offer->user);
    Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();

    $this->assertModelExists($offer);
    $this->assertModelExists($message);

    $this->actingAs($offer->post->user)
        ->get(route('posts.progress.show', $offer))
        ->assertOk()
        ->assertSee('Deleted user')
        ->assertSee('Thanks for the lesson!');
});

test('deleting an account withdraws open offers and posts', function () {
    $user = User::factory()->create();
    $unusedPost = Post::factory()->for($user)->create();
    $postWithHistory = Post::factory()->for($user)->create();
    PostOffer::factory()->for($postWithHistory)->create(['status' => PostOfferStatus::REJECTED]);
    $receivedOffer = PostOffer::factory()->for(Post::factory()->for($user))->create();
    $sentOffer = PostOffer::factory()->for($user)->create();

    $this->actingAs($user);
    Livewire::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();

    $this->assertModelMissing($unusedPost);
    expect($receivedOffer->fresh()->status)->toBe(PostOfferStatus::REJECTED);
    expect($sentOffer->fresh()->status)->toBe(PostOfferStatus::WITHDRAWN);
    expect($postWithHistory->fresh()->status)->toBe(PostStatus::CANCELLED);
    expect($receivedOffer->post->fresh()->status)->toBe(PostStatus::CANCELLED);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});

test('a profile picture can be uploaded and is stored on the public disk', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    // Lietotājs izvēlas attēlu un saglabā profilu
    Livewire::test(Profile::class)
        ->set('username', $user->username)
        ->set('email', $user->email)
        ->set('photo', UploadedFile::fake()->image('avatar.png'))
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    // Fails ir saglabāts un ceļš ierakstīts lietotāja profilā
    $path = $user->refresh()->profile_picture;

    expect($path)->toStartWith('profile-pictures/');
    Storage::disk('public')->assertExists($path);
    expect($user->profilePictureUrl())->not->toBeNull();
});

test('uploading a new profile picture deletes the previous file', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $oldPath = UploadedFile::fake()->image('old.png')->store('profile-pictures', 'public');
    $user->forceFill(['profile_picture' => $oldPath])->save();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('photo', UploadedFile::fake()->image('new.png'))
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->refresh()->profile_picture);
});

test('files that are not small images are rejected as a profile picture', function (UploadedFile $file) {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Profile::class)
        ->set('photo', $file)
        ->assertHasErrors(['photo'])
        ->assertSet('photo', null);

    expect($user->refresh()->profile_picture)->toBeNull();
})->with([
    'a pdf document' => fn () => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
    'an image over 2 MB' => fn () => UploadedFile::fake()->image('huge.png')->size(3000),
]);

test('a profile picture can be removed', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $path = UploadedFile::fake()->image('avatar.png')->store('profile-pictures', 'public');
    $user->forceFill(['profile_picture' => $path])->save();

    $this->actingAs($user);

    Livewire::test(Profile::class)->call('removePhoto');

    Storage::disk('public')->assertMissing($path);
    expect($user->refresh()->profile_picture)->toBeNull();
});

test('deactivating an account deletes the profile picture file', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $path = UploadedFile::fake()->image('avatar.png')->store('profile-pictures', 'public');
    $user->forceFill(['profile_picture' => $path])->save();

    $user->deactivate();

    Storage::disk('public')->assertMissing($path);
    expect($user->refresh()->profile_picture)->toBeNull();
});
