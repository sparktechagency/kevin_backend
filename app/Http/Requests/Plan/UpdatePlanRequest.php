<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanRequest extends FormRequest
{
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
        $planId = $this->route('plan'); // assumes route parameter is {plan}

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'name')->ignore($planId),
            ],
            'stripe_price_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('plans', 'stripe_price_id')->ignore($planId),
            ],
            'stripe_product_id' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'interval' => ['required', Rule::in(['month'])],
            'features' => 'required|array', // array of features
            'features.*' => 'string|max:255', // each feature as string
            'is_active' => 'boolean',
        ];
    }
}
