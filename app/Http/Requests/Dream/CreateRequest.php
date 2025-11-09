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
        $id = $this->route('dream'); // Get the ID if it's an update request

        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',  // Ensure category_id exists in the categories table
            'description' => 'nullable|string|max:1000',  // Allow null or string with max length
            'frequency' => 'required|in:Daily,Weekly,Monthly',  // Ensure frequency is one of these values
            'start_date' => 'nullable|date|after_or_equal:today',  // Ensure start date is either null or a date after or equal to today
            'end_date' => 'nullable|date|after_or_equal:start_date',  // Ensure end date is after the start date if provided
            'from' => 'nullable|date_format:H:i',  // Validate the 'from' time field
            'to' => 'nullable|date_format:H:i|after:from',  // Ensure 'to' is after 'from' if both are provided
            'per_week' => 'nullable|integer|min:1',  // Ensure it's an integer and greater than or equal to 1
            'per_month' => 'nullable|integer|min:1',  // Ensure it's an integer and greater than or equal to 1
            'status' => 'nullable|boolean',  // Ensure status is a boolean value
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
            'name.required' => 'The dream name is required.',
            'name.string' => 'The dream name must be a valid string.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'description.string' => 'The description must be a valid string.',
            'frequency.required' => 'The frequency field is required.',
            'frequency.in' => 'The frequency must be one of the following: Daily, Weekly, Monthly.',
            'start_date.date' => 'The start date must be a valid date.',
            'start_date.after_or_equal' => 'The start date must be today or a future date.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after the start date.',
            'from.date_format' => 'The from time must be in the format HH:MM.',
            'to.date_format' => 'The to time must be in the format HH:MM.',
            'to.after' => 'The to time must be after the from time.',
            'per_week.integer' => 'The per week value must be an integer.',
            'per_week.min' => 'The per week value must be greater than or equal to 1.',
            'per_month.integer' => 'The per month value must be an integer.',
            'per_month.min' => 'The per month value must be greater than or equal to 1.',
            'status.boolean' => 'The status must be either true or false.',
        ];
    }
}
