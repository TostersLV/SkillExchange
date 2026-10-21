<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Title('Profile settings')]
class Profile extends Component
{
    use ProfileValidationRules, WithFileUploads;

    public string $username = '';

    public string $email = '';

    public ?TemporaryUploadedFile $photo = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->username = Auth::user()->username;
        $this->email = Auth::user()->email;
    }

    /**
     * Validate a freshly chosen picture right away so a non-image never reaches the preview.
     */
    public function updatedPhoto(): void
    {
        try {
            $this->validateOnly('photo', ['photo' => $this->photoRules()]);
        } catch (ValidationException $exception) {
            $this->photo = null;

            throw $exception;
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            ...$this->profileRules($user->id),
            'photo' => $this->photoRules(),
        ]);

        $user->fill([
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->photo) {
            $user->removeProfilePicture();
            $user->profile_picture = $this->photo->store('profile-pictures', 'public');
            $this->photo = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Discard the chosen picture, or delete the saved one when nothing new is selected.
     */
    public function removePhoto(): void
    {
        if ($this->photo) {
            $this->photo = null;

            return;
        }

        Auth::user()->removeProfilePicture();

        Flux::toast(variant: 'success', text: __('Profile picture removed.'));
    }

    /**
     * @return array<int, string>
     */
    private function photoRules(): array
    {
        return ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
    }
}
