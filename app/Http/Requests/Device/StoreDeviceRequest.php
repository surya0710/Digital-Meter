<?php

namespace App\Http\Requests\Device;

use App\Enums\DeviceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'device_id' => ['required', 'string', 'max:255', 'unique:assign_device,device_id'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'device_type' => ['nullable', Rule::enum(DeviceType::class)],
            'status' => ['required', Rule::in(['0', '1', 0, 1, true, false])],
        ];
    }
}
