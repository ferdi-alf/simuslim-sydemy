@extends('layouts.dashboard-layouts')
@section('title', 'Rekaman Kajian Page')

@section('content')
    <x-fragments.form-modal id="add-kajian-rekaman-modal" title="Tambah Kajian Rekaman"
        action="{{ route('kajian-rekaman.store') }}">
        <div class="grid space-x-1.5 grid-cols-2">

            <x-fragments.text-field label="Judul" name="judul" required />
            <x-fragments.text-field label="Kitab" name="kitab" />


            <div class="mb-4 col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Kajian <span
                        class="text-red-500">*</span></label>
                <div class="relative">
                    <button type="button" id="kajian-dropdown-btn"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-left bg-white">
                        <span id="kajian-selected-text" class="text-gray-500">Pilih Kajian dan Jadwal</span>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-3 text-gray-400"></i>
                    </button>

                    <div id="kajian-dropdown-menu"
                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                        <div class="max-h-60 overflow-auto">
                            @foreach ($kajianPosters as $poster)
                                @if ($poster->jadwalKajians->count() > 0)
                                    <div class="kajian-group">
                                        <div class="kajian-header px-4 py-2 bg-gray-50 font-medium text-gray-900 border-b cursor-pointer hover:bg-gray-100"
                                            data-kajian-id="{{ $poster->id }}">
                                            <i class="fa-solid fa-chevron-right mr-2 text-xs transition-transform"></i>
                                            {{ $poster->judul }} ({{ $poster->jadwalKajians->count() }} jadwal)
                                        </div>
                                        <div class="jadwal-options hidden">
                                            @foreach ($poster->jadwalKajians as $jadwal)
                                                <div class="jadwal-option px-6 py-2 cursor-pointer hover:bg-indigo-50 text-sm"
                                                    data-value="{{ $jadwal->id }}"
                                                    data-text="{{ $poster->judul }} - {{ $jadwal->tanggal }} ({{ $jadwal->hari }}, {{ $jadwal->jam_mulai }}-{{ $jadwal->jam_selesai }}) - {{ $jadwal->diperuntukan }}">
                                                    <div class="font-medium">{{ $jadwal->tanggal }} - {{ $jadwal->hari }}
                                                    </div>
                                                    <div class="text-gray-500 text-xs">{{ $jadwal->jam_mulai }} -
                                                        {{ $jadwal->jam_selesai }} | {{ $jadwal->diperuntukan }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <input type="hidden" name="jadwal_kajian_id" id="jadwal_kajian_id" required>
                </div>
                <small>hanya jadwal kajian status selesai</small>
            </div>

            <x-fragments.select-field label="Kategori" name="kategori" :options="[['value' => 'video', 'label' => 'Video'], ['value' => 'audio', 'label' => 'Audio']]" required />
            <x-fragments.text-field label="Link" name="link" required />
            <div class="col-span-2">
                <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions"
                    placeholder="Pilih atau ketik nama ustadz baru" required />
            </div>
        </div>
    </x-fragments.form-modal>

    <div class="p-3 col-s rounded-lg bg-white/45 shadow-lg backdrop-blur-3xl">
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-kajian-rekaman-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Kajian Rekaman
            </x-fragments.modal-button>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data Kajian Rekaman</h2>
            <x-reusable-table :headers="['No', 'Judul', 'Kitab', 'Kategori', 'Kajian/Jadwal', 'Link', 'Ustadz']" :data="$data" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->judul,
                fn($row) => $row->kitab ?? '-',
                fn($row) => ucfirst($row->kategori),
                fn($row) => $row->jadwalKajian && $row->jadwalKajian->kajianPoster
                    ? $row->jadwalKajian->kajianPoster->judul .
                        '<br><small class=\'text-gray-500\'>' .
                        $row->jadwalKajian->tanggal .
                        ' (' .
                        $row->jadwalKajian->hari .
                        ')</small>'
                    : '-',
                fn($row) => '<a href=\'' .
                    $row->link .
                    '\' target=\'_blank\' class=\'text-blue-600 hover:underline\'>' .
                    Str::limit($row->link, 30) .
                    '</a>',
                fn($row) => $row->ustadzs->pluck('nama_lengkap')->implode(', '),
            ]" :showActions="true"
                :actionButtons="fn($row) => view('components.action-buttons', [
                                                                                                                                                                                                                                                                                                'modalId' => 'modal-update-kajian-rekaman-' . $row->id,
                                                                                                                                                                                                                                                                                                'updateRoute' => route('kajian-rekaman.update', $row->id),
                                                                                                                                                                                                                                                                                                'deleteRoute' => route('kajian-rekaman.destroy', $row->id),
                                                                                                                                                                                                                                                                                            ])" />
        </div>
    </div>

    @foreach ($data as $kajianRekaman)
        <x-fragments.form-modal id="modal-update-kajian-rekaman-{{ $kajianRekaman->id }}" title="Edit Kajian Rekaman"
            action="{{ route('kajian-rekaman.update', $kajianRekaman->id) }}" method="PUT">
            <div class="grid space-x-1.5 grid-cols-2">
                <x-fragments.text-field label="Judul" name="judul" :value="$kajianRekaman->judul" required />
                <x-fragments.text-field label="Kitab" name="kitab" :value="$kajianRekaman->kitab" />

                <div class="mb-4 col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Kajian <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <button type="button" id="kajian-dropdown-btn-{{ $kajianRekaman->id }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-left bg-white">
                            <span id="kajian-selected-text-{{ $kajianRekaman->id }}" class="text-gray-900">
                                @if ($kajianRekaman->jadwalKajian && $kajianRekaman->jadwalKajian->kajianPoster)
                                    {{ $kajianRekaman->jadwalKajian->kajianPoster->judul }} -
                                    {{ $kajianRekaman->jadwalKajian->tanggal }} ({{ $kajianRekaman->jadwalKajian->hari }},
                                    {{ $kajianRekaman->jadwalKajian->jam_mulai }}-{{ $kajianRekaman->jadwalKajian->jam_selesai }})
                                    - {{ $kajianRekaman->jadwalKajian->diperuntukan }}
                                @else
                                    Pilih Kajian dan Jadwal
                                @endif
                            </span>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-3 text-gray-400"></i>
                        </button>

                        <div id="kajian-dropdown-menu-{{ $kajianRekaman->id }}"
                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                            <div class="max-h-60 overflow-auto">
                                @foreach ($kajianPosters as $poster)
                                    @if ($poster->jadwalKajians->count() > 0)
                                        <div class="kajian-group-{{ $kajianRekaman->id }}">
                                            <div class="kajian-header px-4 py-2 bg-gray-50 font-medium text-gray-900 border-b cursor-pointer hover:bg-gray-100"
                                                data-kajian-id="{{ $poster->id }}"
                                                data-modal-id="{{ $kajianRekaman->id }}">
                                                <i class="fa-solid fa-chevron-right mr-2 text-xs transition-transform"></i>
                                                {{ $poster->judul }} ({{ $poster->jadwalKajians->count() }} jadwal)
                                            </div>
                                            <div class="jadwal-options-{{ $kajianRekaman->id }} hidden">
                                                @foreach ($poster->jadwalKajians as $jadwal)
                                                    <div class="jadwal-option px-6 py-2 cursor-pointer hover:bg-indigo-50 text-sm"
                                                        data-value="{{ $jadwal->id }}"
                                                        data-modal-id="{{ $kajianRekaman->id }}"
                                                        data-text="{{ $poster->judul }} - {{ $jadwal->tanggal }} ({{ $jadwal->hari }}, {{ $jadwal->jam_mulai }}-{{ $jadwal->jam_selesai }}) - {{ $jadwal->diperuntukan }}">
                                                        <div class="font-medium">{{ $jadwal->tanggal }} -
                                                            {{ $jadwal->hari }}
                                                        </div>
                                                        <div class="text-gray-500 text-xs">{{ $jadwal->jam_mulai }} -
                                                            {{ $jadwal->jam_selesai }} | {{ $jadwal->diperuntukan }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <input type="hidden" name="jadwal_kajian_id" id="jadwal_kajian_id_{{ $kajianRekaman->id }}"
                            value="{{ $kajianRekaman->jadwal_kajian_id }}" required>
                    </div>
                </div>

                <x-fragments.select-field label="Kategori" name="kategori" :options="[['value' => 'video', 'label' => 'Video'], ['value' => 'audio', 'label' => 'Audio']]" :value="$kajianRekaman->kategori" required />
                <x-fragments.text-field label="Link" name="link" :value="$kajianRekaman->link" required />
                <div class="col-span-2">
                    <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions" :value="$kajianRekaman->ustadzs->pluck('id')->toArray()"
                        placeholder="Pilih atau ketik nama ustadz baru" required />
                </div>
            </div>
        </x-fragments.form-modal>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupKajianDropdown('', 'kajian-dropdown-btn', 'kajian-dropdown-menu', 'kajian-selected-text',
                'jadwal_kajian_id');

            @foreach ($data as $kajianRekaman)
                setupKajianDropdown('{{ $kajianRekaman->id }}', 'kajian-dropdown-btn-{{ $kajianRekaman->id }}',
                    'kajian-dropdown-menu-{{ $kajianRekaman->id }}',
                    'kajian-selected-text-{{ $kajianRekaman->id }}',
                    'jadwal_kajian_id_{{ $kajianRekaman->id }}');
            @endforeach
        });

        function setupKajianDropdown(modalId, btnId, menuId, textId, inputId) {
            const btn = document.getElementById(btnId);
            const menu = document.getElementById(menuId);
            const selectedText = document.getElementById(textId);
            const hiddenInput = document.getElementById(inputId);

            btn.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!btn.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });

            const kajianHeaders = menu.querySelectorAll('.kajian-header');
            kajianHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const kajianId = this.getAttribute('data-kajian-id');
                    const chevron = this.querySelector('i');
                    const groupClass = modalId ? `kajian-group-${modalId}` : 'kajian-group';
                    const optionsClass = modalId ? `jadwal-options-${modalId}` : 'jadwal-options';

                    const jadwalOptions = this.closest('.' + groupClass).querySelector('.' + optionsClass);

                    jadwalOptions.classList.toggle('hidden');
                    chevron.classList.toggle('fa-chevron-right');
                    chevron.classList.toggle('fa-chevron-down');
                });
            });

            const jadwalOptions = menu.querySelectorAll('.jadwal-option');
            jadwalOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const text = this.getAttribute('data-text');
                    hiddenInput.value = value;
                    selectedText.textContent = text;
                    selectedText.classList.remove('text-gray-500');
                    selectedText.classList.add('text-gray-900');
                    menu.classList.add('hidden');
                    const allHeaders = menu.querySelectorAll('.kajian-header');
                    const allOptions = menu.querySelectorAll('[class*="jadwal-options"]');

                    allHeaders.forEach(h => {
                        const chevron = h.querySelector('i');
                        chevron.classList.remove('fa-chevron-down');
                        chevron.classList.add('fa-chevron-right');
                    });

                    allOptions.forEach(opts => {
                        opts.classList.add('hidden');
                    });
                });
            });
        }
    </script>
@endsection
