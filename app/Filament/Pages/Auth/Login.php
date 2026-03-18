<?php
namespace App\Filament\Pages\Auth;

use Closure;
use Filament\Forms\Form;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form->schema([
            $this->getUsernameFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent(),
        ]);
    }
    
    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autofocus()
            ->rule('regex:/^[a-zA-Z0-9 ]*$/')
            ->validationMessages([
                'required' => 'Username wajib diisi.',
                'regex' => 'Username tidak boleh mengandung karakter khusus.',
            ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function getRateLimitKey($method, $component = null): ?string
    {
        $username = $this->data['username'] ?? 'unknown';

        return request()->ip() . '|' . static::class . '|' . $method . '|' . strtolower($username);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => 'Username atau password tidak valid.',
        ]);
    }
}