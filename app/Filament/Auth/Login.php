<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Hoş geldiniz';
    }

    public function getSubheading(): ?string
    {
        return 'Dernek kitap satış ve muhasebe paneline giriş yapın';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('E-posta')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Şifre')
            ->password()
            ->revealable()
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()
            ->label('Beni hatırla');
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'Girdiğiniz bilgiler hatalı. Lütfen tekrar deneyin.',
        ]);
    }

    protected function getAuthenticateFormAction(): \Filament\Actions\Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Giriş Yap');
    }
}
