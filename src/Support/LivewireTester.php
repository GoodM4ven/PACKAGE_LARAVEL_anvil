<?php

declare(strict_types=1);

namespace GoodMaven\Anvil\Support;

use Pest\Browser\Execution;
use RuntimeException;

final class LivewireTester
{
    /**
     * Waits until the DOM input element reflects the user-typed value.
     *
     * This happens before Livewire updating/updated hooks.
     */
    public static function waitForDomInputValue(
        $page,
        string $selector,
        string $expected
    ): void {
        self::waitForInputValue(
            $page,
            $selector,
            $expected,
            'waitForDomInputValue'
        );
    }

    /**
     * Waits until Livewire has processed the update and
     * rendered the canonical value back into the DOM.
     *
     * Lifecycle-aligned with `rendered()`.
     */
    public static function waitForRenderedInputValue(
        $page,
        string $selector,
        string $expected
    ): void {
        self::waitForInputValue(
            $page,
            $selector,
            $expected,
            'waitForRenderedInputValue'
        );
    }

    /* -----------------------------------------------------------------
     | Internal shared logic
     | -----------------------------------------------------------------
     */

    private static function waitForInputValue(
        $page,
        string $selector,
        string $expected,
        string $caller
    ): void {
        Execution::instance()->waitForExpectation(function () use (
            $page,
            $selector,
            $expected,
            $caller
        ): void {
            $result = self::inspectInput($page, $selector);

            if ($result['ok'] !== true) {
                throw new RuntimeException(
                    self::failureMessage($caller, $selector, $result)
                );
            }

            expect($result['value'])->toBe($expected);
        });
    }

    /**
     * Runs entirely in the browser context.
     *
     * Returns a structured payload so PHP can
     * decide whether to retry or fail fast.
     */
    private static function inspectInput($page, string $selector): array
    {
        return $page->script(<<<JS
            (() => {
                const el = document.querySelector('{$selector}');

                if (!el) {
                    return { ok: false, reason: 'not-found' };
                }

                const tag = el.tagName.toLowerCase();

                const isInput =
                    tag === 'input' ||
                    tag === 'textarea' ||
                    tag === 'select' ||
                    el.isContentEditable;

                if (!isInput) {
                    return { ok: false, reason: 'not-input', tag };
                }

                return {
                    ok: true,
                    value: el.value ?? el.textContent ?? ''
                };
            })()
        JS);
    }

    private static function failureMessage(
        string $caller,
        string $selector,
        array $result
    ): string {
        return match ($result['reason'] ?? null) {
            'not-found' => "{$caller}(): Selector '{$selector}' was not found in the DOM.",

            'not-input' => "{$caller}(): Selector '{$selector}' is not an input element (found <{$result['tag']}>).",

            default => "{$caller}(): Unknown failure while inspecting selector '{$selector}'.",
        };
    }
}
