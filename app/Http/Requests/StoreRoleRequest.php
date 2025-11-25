<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Available guards for roles
     */
    private const AVAILABLE_GUARDS = ['web', 'api'];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
            'guard_name' => 'nullable|string|in:' . implode(',', self::AVAILABLE_GUARDS),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del rol es requerido.',
            'name.unique' => 'Ya existe un rol con ese nombre.',
            'permissions.*.exists' => 'Uno o más permisos seleccionados no existen.',
            'guard_name.in' => 'El guardia especificado no es válido.',
        ];
    }
}
