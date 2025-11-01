<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    private $successCount = 0;
    private $errorRows = [];

    public function model(array $row)
    {
        try {
            // Generate a temporary password for new users
            $temporaryPassword = config('app.default_password');

            $user = User::create([
                'name' => ucwords(strtolower(trim($row['name']))),
                'email' => strtolower(trim($row['email'])),
                'password' => $temporaryPassword,
                'password_changed_at' => null,
                'active' => $this->parseBooleanValue($row['active'] ?? 'true'),
                'require_2fa' => $this->parseBooleanValue($row['require_2fa'] ?? 'false'),
                'expires_at' => isset($row['expires_at']) && !empty($row['expires_at'])
                    ? \Carbon\Carbon::parse($row['expires_at'])
                    : null,
            ]);

            // Assign role
            $role = strtolower(trim($row['role'] ?? 'user'));
            if (in_array($role, ['superadmin', 'admin', 'user'])) {
                $user->assignRole($role);
            } else {
                $user->assignRole('user');
            }

            // If 2FA is required, enable it automatically
            if ($user->require_2fa) {
                $secret = app(\Laravel\Fortify\TwoFactorAuthenticationProvider::class)->generateSecretKey();
                $user->forceFill([
                    'two_factor_secret' => encrypt($secret),
                    'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1, 8))->map(function () {
                        return Str::random(10).'-'.Str::random(10);
                    })->toArray())),
                    'two_factor_confirmed_at' => now(),
                ])->save();
            }

            // Send welcome email with temporary password
            $user->notify(new \App\Notifications\NewUserCreated($temporaryPassword));

            $this->successCount++;

            return $user;
        } catch (\Exception $e) {
            $this->errorRows[] = [
                'row' => $row,
                'error' => $e->getMessage()
            ];
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['nullable', 'string', 'in:superadmin,admin,user'],
            'active' => ['nullable'],
            'require_2fa' => ['nullable'],
            'expires_at' => ['nullable', 'date'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'role.in' => 'The role must be superadmin, admin, or user.',
        ];
    }

    private function parseBooleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['true', 'yes', '1', 'si', 'sí'], true);
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrors(): array
    {
        return $this->errorRows;
    }

    public function getFailures(): array
    {
        return $this->failures();
    }
}
