{{-- resources/views/components/fragments/currency-field.blade.php --}}
@props([
    'label' => '',
    'name' => '',
    'placeholder' => 'Masukkan harga...',
    'value' => '',
    'required' => false,
    'currency' => 'Rp',
])

@php
    $hasError = $errors->has($name);
    $errorClass = $hasError
        ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
        : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500';

    // Clean value - extract only numeric
    $cleanValue = is_numeric($value) ? $value : preg_replace('/[^\d]/', '', $value);
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-gray-900">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($currency)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 text-sm">{{ $currency }}</span>
            </div>
        @endif

        <input type="text" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, '') }}"
            placeholder="{{ $placeholder }}"
            class="bg-gray-50 border {{ $errorClass }} text-gray-900 text-sm rounded-lg block w-full {{ $currency ? 'pl-12' : 'pl-3' }} pr-3 py-2.5 transition-colors duration-200"
            {{ $required ? 'required' : '' }} {{ $attributes }} data-currency-input
            data-raw-value="{{ $cleanValue }}" />

        <input type="hidden" name="{{ $name }}_numeric" id="{{ $name }}_numeric"
            value="{{ old($name . '_numeric', $cleanValue) }}">
    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-600 flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-1"></i>
            {{ $message }}
        </p>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatCurrencyIndonesian(value) {
            const numericValue = value.toString().replace(/[^\d]/g, '');
            if (!numericValue) return '';
            return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function extractNumericValue(value) {
            return value.toString().replace(/[^\d]/g, '');
        }

        function updateHiddenField(input, hiddenInput, numericValue) {
            if (hiddenInput) {
                hiddenInput.value = numericValue;
                hiddenInput.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
                console.log('Hidden field updated:', hiddenInput.name, '=', numericValue);
            }
        }

        function syncCurrencyFields(input) {
            const hiddenInput = document.getElementById(input.id + '_numeric');
            const numericValue = extractNumericValue(input.value);
            const formattedValue = formatCurrencyIndonesian(numericValue);

            input.value = formattedValue;
            updateHiddenField(input, hiddenInput, numericValue);

            return {
                numericValue,
                formattedValue
            };
        }

        function initializeCurrencyInput(input) {
            if (input.hasAttribute('data-currency-initialized')) {
                return;
            }

            const hiddenInput = document.getElementById(input.id + '_numeric');
            const rawValue = input.getAttribute('data-raw-value') || input.value || '';

            // Initialize with proper formatting
            if (rawValue) {
                const numericValue = extractNumericValue(rawValue);
                const formattedValue = formatCurrencyIndonesian(numericValue);
                input.value = formattedValue;
                updateHiddenField(input, hiddenInput, numericValue);
            }

            input.setAttribute('data-currency-initialized', 'true');

            // Handle all input events
            ['input', 'keyup', 'change'].forEach(eventType => {
                input.addEventListener(eventType, function(e) {
                    const cursorPosition = e.target.selectionStart;
                    const oldLength = e.target.value.length;

                    const result = syncCurrencyFields(e.target);

                    // Maintain cursor position for input events only
                    if (eventType === 'input') {
                        const newLength = result.formattedValue.length;
                        const lengthDifference = newLength - oldLength;
                        const newCursorPosition = Math.max(0, cursorPosition +
                        lengthDifference);

                        requestAnimationFrame(() => {
                            e.target.setSelectionRange(newCursorPosition,
                                newCursorPosition);
                        });
                    }
                });
            });

            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const numericValue = extractNumericValue(pastedText);
                const formattedValue = formatCurrencyIndonesian(numericValue);

                e.target.value = formattedValue;
                updateHiddenField(input, hiddenInput, numericValue);
            });

            // Handle focus - select all
            input.addEventListener('focus', function(e) {
                requestAnimationFrame(() => {
                    e.target.select();
                });
            });

            // Force sync before form submission
            const form = input.closest('form');
            if (form && !form.hasAttribute('data-currency-form-handler')) {
                form.setAttribute('data-currency-form-handler', 'true');

                // Handle form submission
                form.addEventListener('submit', function(e) {
                    console.log('Form submitting - syncing currency fields...');
                    const currencyInputs = form.querySelectorAll('[data-currency-input]');
                    currencyInputs.forEach(syncCurrencyFields);
                });

                // Handle button clicks (for forms with PUT/PATCH methods)
                form.addEventListener('click', function(e) {
                    if (e.target.type === 'submit' || e.target.closest('button[type="submit"]')) {
                        console.log('Submit button clicked - syncing currency fields...');
                        const currencyInputs = form.querySelectorAll('[data-currency-input]');
                        currencyInputs.forEach(syncCurrencyFields);
                    }
                });
            }
        }

        function initializeAllCurrencyInputs() {
            document.querySelectorAll('[data-currency-input]:not([data-currency-initialized])').forEach(
            input => {
                initializeCurrencyInput(input);
            });
        }

        // Initialize on DOM ready
        initializeAllCurrencyInputs();

        // Re-initialize for dynamically added content (modals)
        const observer = new MutationObserver(function(mutations) {
            let shouldReinitialize = false;

            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            if (node.querySelector && node.querySelector(
                                    '[data-currency-input]')) {
                                shouldReinitialize = true;
                            }
                            if (node.hasAttribute && node.hasAttribute(
                                    'data-currency-input')) {
                                shouldReinitialize = true;
                            }
                        }
                    });
                }
            });

            if (shouldReinitialize) {
                setTimeout(initializeAllCurrencyInputs, 100);
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Global function for manual sync (debugging)
        window.syncAllCurrencyInputs = function() {
            document.querySelectorAll('[data-currency-input]').forEach(syncCurrencyFields);
            console.log('All currency inputs synchronized');
        };
    });
</script>
