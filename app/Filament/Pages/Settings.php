<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationLabel = 'Ayarlar';

    protected static ?string $title = 'Ayarlar';

    protected static ?string $navigationGroup = 'Tanımlar';

    protected static ?int $navigationSort = 99;

    public ?array $profileData = [];

    public ?array $passwordData = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->profileForm->fill([
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->passwordForm->fill();
    }

    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
        ];
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hesap Bilgileri')
                    ->description('Giriş e-postası ve görünen ad buradan güncellenir')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad Soyad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                Rule::unique('users', 'email')->ignore(auth()->id()),
                            ]),
                    ])
                    ->columns(2),
            ])
            ->statePath('profileData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Şifre Değiştir')
                    ->description('Yeni şifre en az 8 karakter olmalıdır. Değişiklik sonrası mevcut oturum devam eder.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Mevcut Şifre')
                            ->password()
                            ->revealable()
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->label('Yeni Şifre')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::defaults())
                            ->confirmed(),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Yeni Şifre (Tekrar)')
                            ->password()
                            ->revealable()
                            ->required()
                            ->dehydrated(false),
                    ]),
            ])
            ->statePath('passwordData');
    }

    public function updateProfile(): void
    {
        $data = $this->profileForm->getState();

        auth()->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        Notification::make()
            ->title('Hesap bilgileri güncellendi')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $data = $this->passwordForm->getState();

        if (! Hash::check($data['current_password'], auth()->user()->password)) {
            throw ValidationException::withMessages([
                'passwordData.current_password' => 'Mevcut şifre hatalı.',
            ]);
        }

        auth()->user()->update([
            'password' => $data['password'],
        ]);

        $this->passwordForm->fill([
            'current_password' => null,
            'password' => null,
            'password_confirmation' => null,
        ]);

        Notification::make()
            ->title('Şifre başarıyla değiştirildi')
            ->body('Bundan sonra yeni şifrenizle giriş yapabilirsiniz.')
            ->success()
            ->send();
    }

    public function getSubheading(): ?string
    {
        return 'Hesap ve güvenlik ayarlarını buradan yönetin';
    }
}
