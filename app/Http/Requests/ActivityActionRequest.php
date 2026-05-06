<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $activity = $this->route('activity');
        
        // Vérifier que l'utilisateur est un stagiaire et que l'activité lui appartient
        return $user->role->name === 'stagiaire' && $activity->stagiaire_id === $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $action = $this->input('action');
        
        $rules = [
            'action' => [
                'required',
                'string',
                Rule::in(['soumettre_livrable', 'refuser', 'demander_info'])
            ]
        ];

        // Règles spécifiques selon l'action
        switch ($action) {
            case 'soumettre_livrable':
                $rules['commentaire'] = [
                    'required',
                    'string',
                    'min:10',
                    'max:1000'
                ];
                $rules['fichier'] = [
                    'nullable',
                    'file',
                    'max:10240', // 10MB
                    'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,txt,jpg,jpeg,png,gif'
                ];
                break;

            case 'refuser':
                $rules['raison'] = [
                    'required',
                    'string',
                    'min:10',
                    'max:500'
                ];
                break;

            case 'demander_info':
                $rules['question'] = [
                    'required',
                    'string',
                    'min:10',
                    'max:500'
                ];
                break;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'L\'action est obligatoire.',
            'action.in' => 'L\'action spécifiée n\'est pas valide.',
            'commentaire.required' => 'Le commentaire est obligatoire.',
            'commentaire.min' => 'Le commentaire doit contenir au moins 10 caractères.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
            'fichier.max' => 'Le fichier ne peut pas dépasser 10MB.',
            'fichier.mimes' => 'Le type de fichier n\'est pas autorisé.',
            'raison.required' => 'La raison du refus est obligatoire.',
            'raison.min' => 'La raison doit contenir au moins 10 caractères.',
            'raison.max' => 'La raison ne peut pas dépasser 500 caractères.',
            'question.required' => 'La question est obligatoire.',
            'question.min' => 'La question doit contenir au moins 10 caractères.',
            'question.max' => 'La question ne peut pas dépasser 500 caractères.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'action' => 'action',
            'commentaire' => 'commentaire',
            'fichier' => 'fichier',
            'raison' => 'raison',
            'question' => 'question',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $activity = $this->route('activity');
            $action = $this->input('action');

            // Vérifier que l'activité peut recevoir cette action
            if ($activity && $action) {
                switch ($action) {
                    case 'soumettre_livrable':
                        if (!in_array($activity->statut, ['assignee', 'en_cours'])) {
                            $validator->errors()->add('action', 'Cette activité ne peut pas recevoir de livrable.');
                        }
                        break;

                    case 'refuser':
                        if (!in_array($activity->statut, ['assignee', 'en_cours'])) {
                            $validator->errors()->add('action', 'Cette activité ne peut pas être refusée.');
                        }
                        break;

                    case 'demander_info':
                        if (!in_array($activity->statut, ['assignee', 'en_cours', 'soumise'])) {
                            $validator->errors()->add('action', 'Cette activité ne peut pas recevoir de demande d\'information.');
                        }
                        break;
                }
            }
        });
    }
}
