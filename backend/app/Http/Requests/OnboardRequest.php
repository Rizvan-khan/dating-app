<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnboardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'dob' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
            'bio' => ['required', 'string', 'min:20', 'max:500'],
            'area' => ['required', Rule::in(config('bareilly.areas'))],
            'lat' => ['required', 'numeric', 'between:27.5,29.5'],
            'long' => ['required', 'numeric', 'between:78.5,80.5'],
            'photos' => ['required', 'array', 'min:2', 'max:6'],
            'photos.*' => ['required', 'url'],
        ];
    }
}
