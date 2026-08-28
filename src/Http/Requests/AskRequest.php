<?php

declare(strict_types=1);

namespace Murkrow\Rag\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Murkrow\Rag\Chat\ChatAbilities;
use Murkrow\Rag\Data\RetrievalOptions;
use Murkrow\Rag\Sources\SourceRegistry;

/**
 * The one place a chat question turns into retrieval options.
 *
 * Hiding a control in the template is a presentation decision. This is the
 * authorization: a field whose ability is denied is dropped before validation
 * even sees it, so posting `top_k=30` by hand to an account that may not tune
 * retrieval gets the configured default, not an error and not the value.
 */
class AskRequest extends FormRequest
{
    /**
     * @var array<string, bool>|null
     */
    private ?array $allowed = null;

    public function authorize(): bool
    {
        return ChatAbilities::allows('view', $this->user());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:2', 'max:4000'],
            // Not validated as a uuid: an unusable thread id means "this
            // thread is gone", which is a reason to start a new one, never a
            // reason to refuse to answer the question. AskController resolves
            // it or opens a fresh conversation.
            'conversation' => ['sometimes', 'nullable', 'string', 'max:64'],
            'model' => ['sometimes', 'nullable', 'string', Rule::in($this->modelKeys())],
            'sources' => ['sometimes', 'array'],
            'sources.*' => ['string', Rule::in(app(SourceRegistry::class)->keys())],
            // The ceiling matches rag.settings.overridable's, so a value the
            // control panel accepts can never be rejected here.
            'top_k' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'min_score' => ['sometimes', 'numeric', 'min:0', 'max:1'],
            'retrieval_only' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Strip everything this user is not allowed to set, before validation.
     */
    protected function prepareForValidation(): void
    {
        $drop = [];

        if (! $this->allows('model')) {
            $drop[] = 'model';
        }

        if (! $this->allows('sources')) {
            $drop[] = 'sources';
        }

        if (! $this->allows('advanced')) {
            $drop = [...$drop, 'top_k', 'min_score', 'retrieval_only'];
        }

        if ($drop !== []) {
            $this->replace($this->except($drop));
        }
    }

    public function question(): string
    {
        return trim((string) $this->validated('question'));
    }

    public function model(): ?string
    {
        $model = $this->validated('model');

        return is_string($model) && $model !== '' ? $model : null;
    }

    public function retrievalOnly(): bool
    {
        return (bool) $this->validated('retrieval_only', false);
    }

    /**
     * Built through RetrievalOptions::fromArray so the chat speaks exactly the
     * same request vocabulary as the MCP tools and the console commands.
     */
    public function retrievalOptions(): RetrievalOptions
    {
        return RetrievalOptions::fromArray(array_filter([
            'sources' => $this->validated('sources'),
            'top_k' => $this->validated('top_k'),
            'min_score' => $this->validated('min_score'),
        ], static fn ($value): bool => $value !== null && $value !== []));
    }

    /**
     * What the answer was produced under, stored on the conversation so
     * reopening it restores the same settings.
     *
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return array_filter([
            'model' => $this->model(),
            'sources' => $this->validated('sources'),
            'top_k' => $this->validated('top_k'),
            'min_score' => $this->validated('min_score'),
        ], static fn ($value): bool => $value !== null && $value !== []);
    }

    private function allows(string $ability): bool
    {
        $this->allowed ??= ChatAbilities::allowed($this->user());

        return $this->allowed[$ability] ?? false;
    }

    /**
     * @return array<int, string>
     */
    private function modelKeys(): array
    {
        return array_keys((array) config('rag.llm.available_models', []));
    }
}
