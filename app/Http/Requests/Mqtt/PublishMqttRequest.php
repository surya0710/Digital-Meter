<?php

namespace App\Http\Requests\Mqtt;

use Illuminate\Foundation\Http\FormRequest;

class PublishMqttRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'topic' => ['required', 'string'],
            'message' => ['required'],
        ];
    }
}
