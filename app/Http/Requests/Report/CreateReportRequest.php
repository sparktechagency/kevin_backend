<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'manager_id'    => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'schedule'      => 'nullable|in:daily,weekly,monthly,date-range',
            'description'   => 'nullable|string',
            'metrics'       => 'nullable|array',
            'metrics.*'     => 'required|in:engagement_score,goal_completion,user_activity,goal_category,department_metrics,manager_impact,user_retention,roi_metrics',
            'date_range'    => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'manager_id.required'    => 'Manager is required.',
            'manager_id.exists'      => 'Selected manager is invalid.',

            'department_id.required' => 'Department is required.',
            'department_id.exists'   => 'Selected department is invalid.',

            'name.required'          => 'Report name is required.',

            'schedule.in'            => 'Schedule must be one of: daily, weekly, monthly, date-range.',

            'metrics.*.in'           => 'Invalid metric type selected.',
        ];
    }
}

