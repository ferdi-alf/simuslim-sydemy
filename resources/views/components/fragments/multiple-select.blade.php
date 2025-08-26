@props([
    'label' => '',
    'name' => '',
    'options' => [],
    'value' => [],
    'placeholder' => 'Pilih ' . strtolower($label),
    'required' => false,
    'id' => null,
    'allowCreate' => true,
    'createText' => 'Tambah baru: ',
])

@php
    $componentId = $id ?? 'multiple-select-' . str_replace(['[', ']', '.'], '', $name) . '-' . uniqid();
    $selectedValues = is_array($value) ? $value : (is_string($value) ? explode(',', $value) : []);
    $selectedValues = array_map('strval', $selectedValues);
@endphp

<div class="mb-4">
    @if ($label)
        <label for="{{ $componentId }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative" x-data="{
        isOpen: false,
        searchQuery: '',
        selectedValues: @js($selectedValues),
        selectedLabels: @js(collect($options)->pluck('label', 'value')->toArray()),
        options: @js($options),
        highlightedIndex: -1,
        allowCreate: @js($allowCreate),
        createText: @js($createText),
    
        get selectedItems() {
            return this.selectedValues.map(value => {
                // Check if it's an existing option
                const option = this.options.find(option => String(option.value) === String(value));
                if (option) {
                    return option;
                }
                // It's a custom created item
                return {
                    value: value,
                    label: this.selectedLabels[value] || value,
                    isCustom: true
                };
            }).filter(Boolean);
        },
    
        get filteredOptions() {
            if (!this.searchQuery) return this.options;
            return this.options.filter(option =>
                option.label.toLowerCase().includes(this.searchQuery.toLowerCase()) &&
                !this.selectedValues.includes(String(option.value))
            );
        },
    
        get showCreateOption() {
            if (!this.allowCreate || !this.searchQuery.trim()) return false;
    
            // Don't show if exact match exists in options
            const exactMatch = this.options.find(option =>
                option.label.toLowerCase() === this.searchQuery.toLowerCase()
            );
    
            // Don't show if already selected
            const alreadySelected = this.selectedValues.some(value =>
                (this.selectedLabels[value] || value).toLowerCase() === this.searchQuery.toLowerCase()
            );
    
            return !exactMatch && !alreadySelected;
        },
    
        selectOption(option) {
            if (!this.selectedValues.includes(String(option.value))) {
                this.selectedValues.push(String(option.value));
                this.selectedLabels[option.value] = option.label;
            }
            this.searchQuery = '';
            this.isOpen = false;
            this.highlightedIndex = -1;
        },
    
        createAndSelect() {
            if (!this.showCreateOption) return;
    
            const newValue = 'new_' + Date.now();
            const newLabel = this.searchQuery.trim();
    
            this.selectedValues.push(newValue);
            this.selectedLabels[newValue] = newLabel;
    
            this.searchQuery = '';
            this.isOpen = false;
            this.highlightedIndex = -1;
        },
    
        removeItem(value) {
            this.selectedValues = this.selectedValues.filter(v => String(v) !== String(value));
            delete this.selectedLabels[value];
        },
    
        selectHighlighted() {
            const totalItems = this.filteredOptions.length + (this.showCreateOption ? 1 : 0);
    
            if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredOptions.length) {
                this.selectOption(this.filteredOptions[this.highlightedIndex]);
            } else if (this.showCreateOption && this.highlightedIndex === this.filteredOptions.length) {
                this.createAndSelect();
            }
        },
    
        navigateDown() {
            this.isOpen = true;
            const maxIndex = this.filteredOptions.length + (this.showCreateOption ? 1 : 0) - 1;
            this.highlightedIndex = Math.min(this.highlightedIndex + 1, maxIndex);
        },
    
        navigateUp() {
            this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1);
        }
    }" @click.away="isOpen = false">

        <div class="min-h-[42px] border border-gray-300 rounded-md px-3 py-2 bg-white focus-within:ring-1 focus-within:ring-blue-500 focus-within:border-blue-500 cursor-text"
            @click="$refs.searchInput.focus()">

            <!-- Selected Items -->
            <div class="flex flex-wrap gap-1 mb-1" x-show="selectedItems.length > 0">
                <template x-for="item in selectedItems" :key="item.value">
                    <div class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full"
                        :class="item.isCustom ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'">
                        <span x-text="item.label"></span>
                        <button type="button" class="ml-1 hover:text-red-600 focus:outline-none"
                            @click.stop="removeItem(item.value)">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Search Input -->
            <input type="text" x-ref="searchInput" :id="'{{ $componentId }}'"
                class="w-full border-0 p-0 focus:ring-0 focus:outline-none placeholder-gray-400 text-sm"
                :placeholder="selectedItems.length === 0 ? '{{ $placeholder }}' : 'Ketik untuk mencari atau menambah baru...'"
                x-model="searchQuery" @focus="isOpen = true" @keydown.enter.prevent="selectHighlighted()"
                @keydown.escape.prevent="isOpen = false" @keydown.arrow-down.prevent="navigateDown()"
                @keydown.arrow-up.prevent="navigateUp()" autocomplete="off">
        </div>

        <!-- Dropdown Options -->
        <div x-show="isOpen" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">

            <!-- No Results Message -->
            <div x-show="filteredOptions.length === 0 && !showCreateOption" class="px-3 py-2 text-gray-500 text-sm">
                <span x-show="options.length === 0">Tidak ada pilihan tersedia</span>
                <span x-show="options.length > 0 && searchQuery">Tidak ditemukan hasil untuk "<span
                        x-text="searchQuery"></span>"</span>
            </div>

            <!-- Existing Options -->
            <template x-for="(option, index) in filteredOptions" :key="option.value">
                <div class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm flex items-center justify-between"
                    :class="{
                        'bg-blue-50': index === highlightedIndex,
                        'text-gray-400 bg-gray-50': selectedValues.includes(String(option.value))
                    }"
                    @click="!selectedValues.includes(String(option.value)) && selectOption(option)">
                    <span x-text="option.label"></span>
                    <span x-show="selectedValues.includes(String(option.value))" class="text-blue-600">✓</span>
                </div>
            </template>

            <!-- Create New Option -->
            <div x-show="showCreateOption"
                class="px-3 py-2 cursor-pointer hover:bg-green-50 text-sm border-t border-gray-200 bg-green-25"
                :class="{ 'bg-green-50': highlightedIndex === filteredOptions.length }" @click="createAndSelect()">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="text-green-700">
                        <span x-text="createText"></span><strong x-text="searchQuery"></strong>
                    </span>
                </div>
                <p class="text-xs text-green-600 ml-6">Ustadz baru akan dibuat otomatis</p>
            </div>
        </div>

        <!-- Hidden Inputs -->
        <template x-for="(value, index) in selectedValues" :key="'hidden-' + index">
            <input type="hidden" :name="'{{ $name }}[]'"
                :value="selectedLabels[value] && selectedLabels[value] !== value ? selectedLabels[value] : value">
        </template>
    </div>

    @if ($allowCreate)
        <p class="text-xs text-gray-500 mt-1">
            <i class="fa-solid fa-lightbulb mr-1"></i>
            Tip: Ketik nama ustadz baru jika tidak ditemukan dalam daftar
        </p>
    @endif
</div>
