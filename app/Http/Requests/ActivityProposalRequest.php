<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role->name === 'stagiaire';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'titre' => [
                'required',
                'string',
                'min:5',
                'max:255',
                'regex:/^[a-zA-Z0-9\sàâäéèêëïîôöùûüÿçñ\-_.,!?()]+$/'
            ],
            'description' => [
                'required',
                'string',
                'min:20',
                'max:2000'
            ],
            'objectifs' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'priorite' => [
                'required',
                'string',
                Rule::in(['basse', 'moyenne', 'haute', 'urgente'])
            ],
            'date_limite' => [
                'nullable',
                'date',
                'after:today'
            ],
            'duree_estimee' => [
                'nullable',
                'string',
                Rule::in(['1-2 jours', '3-5 jours', '1 semaine', '2 semaines', '1 mois'])
            ],
            'ressources' => [
                'nullable',
                'string',
                'max:500'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.min' => 'Le titre doit contenir au moins 5 caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'titre.regex' => 'Le titre contient des caractères non autorisés.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'objectifs.max' => 'Les objectifs ne peuvent pas dépasser 1000 caractères.',
            'priorite.required' => 'La priorité est obligatoire.',
            'priorite.in' => 'La priorité sélectionnée n\'est pas valide.',
            'date_limite.after' => 'La date limite doit être postérieure à aujourd\'hui.',
            'duree_estimee.in' => 'La durée estimée sélectionnée n\'est pas valide.',
            'ressources.max' => 'Les ressources ne peuvent pas dépasser 500 caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'titre' => 'titre',
            'description' => 'description',
            'objectifs' => 'objectifs',
            'priorite' => 'priorité',
            'date_limite' => 'date limite',
            'duree_estimee' => 'durée estimée',
            'ressources' => 'ressources',
        ];
    }
}
