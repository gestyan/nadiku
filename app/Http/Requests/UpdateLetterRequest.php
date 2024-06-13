<?php

namespace App\Http\Requests;

use App\Enums\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'disposition_number' => __('model.letter.disposition_number'),
            'from' => __('model.letter.from'),
            'to' => __('model.letter.to'),
            'reference_number' => __('model.letter.reference_number'),
            'received_date' => __('model.letter.received_date'),
            'letter_date' => __('model.letter.letter_date'),
            'description' => __('model.letter.description'),
            'note' => __('model.letter.note'),
            'classification_code' => __('model.letter.classification_code'),
            'send_status' => __('model.letter.send_status'),
            'esign_status' => __('model.letter.esign_status'),
            'cc' => __('model.letter.cc'),
            'status' => __('model.letter.status'),
            'number' => __('model.letter.status'),
          	'satker' => __('model.letter.satker'),
          	'to_email' => __('model.letter.to_email'),
          	'bcc' => __('model.letter.bcc'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'disposition_number' => ['nullable'],
            'from' => [Rule::requiredIf($this->type == LetterType::INCOMING->type())],
            'to' => [Rule::requiredIf($this->type == LetterType::OUTGOING->type())],
            'reference_number' => ['required', Rule::unique('letters', 'reference_number')->ignore($this->id)],
            'type' => ['required'],
            'received_date' => [Rule::requiredIf($this->type == LetterType::INCOMING->type())],
            'letter_date' => ['required'],
            'description' => ['nullable'],
            'note' => ['nullable'],
            'classification_code' => ['required'],
            'send_status' => ['nullable'],
            'esign_status' => ['nullable'],
            'cc' => ['nullable'],
            'status' => ['required'],
            'number' => ['required'],
          	'satker' => ['nullable'],
          	'to_email' => ['nullable'],
          	'bcc' => ['nullable'],
        ];
    }
}
