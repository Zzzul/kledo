<?php

namespace App\Http\Requests\ApprovalStages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApprovalStageRequest extends FormRequest
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
            'approver_id' => ['required', Rule::exists(table: 'approvers', column: 'id'), Rule::unique(table: 'approval_stages', column: 'approver_id')->ignore(id: $this->route(param: 'id'))],
        ];
    }
}
