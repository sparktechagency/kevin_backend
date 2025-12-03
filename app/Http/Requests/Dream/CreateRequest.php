<?php

namespace App\Http\Requests\Dream;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
        return [
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'nullable|string|max:1000',
            'frequency'     => 'required|in:Daily,Weekly,Monthly,Yearly,Quarterly',

            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',

            'per_week'      => 'nullable|integer|min:1',
            'per_month'     => 'nullable|integer|min:1',
            'per_quarter'   => 'nullable|integer|min:1',
            'per_year'      => 'nullable|integer|min:1',

            'goal'           => 'nullable|array',
            'goal.*'        => 'string',
            'status'        => 'nullable|boolean',
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'         => 'The dream name is required.',
            'name.string'           => 'The dream name must be a valid string.',

            'category_id.required'  => 'The category is required.',
            'category_id.exists'    => 'The selected category is invalid.',

            'description.string'    => 'The description must be a valid string.',

            'frequency.required'    => 'The frequency field is required.',
            'frequency.in'          => 'The frequency must be one of: Daily, Weekly, Monthly, Yearly, Quarterly.',

            'start_date.date'               => 'The start date must be a valid date.',
            'start_date.after_or_equal'     => 'The start date must be today or a future date.',

            'end_date.date'                 => 'The end date must be a valid date.',
            'end_date.after_or_equal'       => 'The end date must be after the start date.',


            'per_week.integer'      => 'The per week value must be an integer.',
            'per_week.min'          => 'The per week value must be at least 1.',

            'per_month.integer'     => 'The per month value must be an integer.',
            'per_month.min'         => 'The per month value must be at least 1.',

            'per_quarter.integer'   => 'The per quarter value must be an integer.',
            'per_quarter.min'       => 'The per quarter value must be at least 1.',

            'per_year.integer'      => 'The per year value must be an integer.',
            'per_year.min'          => 'The per year value must be at least 1.',

            'goal.array'       => 'The goal field must be an array.',
            'goal.*.string'    => 'Each goal item must be a text value.',

            'status.boolean'        => 'The status must be true or false.',
        ];
    }
}
