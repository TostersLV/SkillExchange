<?php

namespace App\Livewire\Settings;

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user's account, keeping the history other members share with them.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        $user = Auth::user();

        if ($user->hasActiveExchanges()) {
            $this->addError('password', __('Finish or cancel your exchanges in progress before deleting your account.'));

            return;
        }

        $logout();
        $user->deactivate();

        $this->redirect('/', navigate: true);
    }
}
