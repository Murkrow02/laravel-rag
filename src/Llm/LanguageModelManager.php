<?php

declare(strict_types=1);

namespace Murkrow\Rag\Llm;

use Closure;
use Illuminate\Support\Manager;
use Murkrow\Rag\Contracts\LanguageModel;

/**
 * @method LanguageModel driver(string|null $driver = null)
 */
final class LanguageModelManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return (string) $this->config->get('rag.llm.driver', 'prism');
    }

    public function createPrismDriver(): LanguageModel
    {
        return PrismLanguageModel::fromConfig();
    }

    public function createFakeDriver(): LanguageModel
    {
        return new FakeLanguageModel;
    }

    /**
     * @param  Closure(\Illuminate\Contracts\Container\Container): LanguageModel  $callback
     */
    public function register(string $name, Closure $callback): self
    {
        $this->extend($name, $callback);

        return $this;
    }
}
