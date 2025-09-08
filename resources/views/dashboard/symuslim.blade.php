@extends('layouts.dashboard-layouts')
@section('title', 'Data Add-Ons')

@section('content')
    <!-- Modal Tambah Bacaan -->
    <x-fragments.form-modal id="add-bacaan-modal" title="Tambah Bacaan" action="{{ route('symuslim.store') }}">
        <div class="grid grid-cols-2 gap-2">
            <x-fragments.text-field label="Judul" name="judul" required />
            <x-fragments.select-field label="Type" name="type"
                :options="['doa' => 'Doa', 'hadits' => 'Hadits', 'dzikir' => 'Dzikir']" required />
            <div class="col-span-2">
                <x-fragments.textarea-field label="Deskripsi" name="deskripsi" rows="4" />
            </div>
        </div>
    </x-fragments.form-modal>

    <!-- Modal Edit & Detail untuk setiap Bacaan -->
    @foreach ($bacaans as $bacaan)
        <x-fragments.form-modal id="edit-bacaan-modal-{{ $bacaan->id }}" title="Edit Bacaan"
            action="{{ route('symuslim.update', $bacaan->id) }}" method="PUT">
            <div class="grid grid-cols-2 gap-2">
                <x-fragments.text-field label="Judul" name="judul" :value="$bacaan->judul" required />
                <x-fragments.select-field label="Type" name="type"
                    :options="['doa' => 'Doa', 'hadits' => 'Hadits', 'dzikir' => 'Dzikir']"
                    :value="$bacaan->type" required />
                <div class="col-span-2">
                    <x-fragments.textarea-field label="Deskripsi" name="deskripsi"
                        :value="$bacaan->deskripsi" rows="4" />
                </div>
            </div>
        </x-fragments.form-modal>

        <x-fragments.form-modal id="add-detail-modal-{{ $bacaan->id }}" title="Tambah Detail Bacaan"
            action="{{ route('symuslim.store-detail', $bacaan->id) }}">
            <div class="grid grid-cols-2 gap-2">
                <div class="col-span-2">
                    <x-fragments.textarea-field label="Arab" name="arab" rows="3" />
                    <x-fragments.textarea-field label="Latin" name="latin" rows="3" />
                    <x-fragments.textarea-field label="Terjemahan" name="terjemahan" rows="3" />
                    <x-fragments.text-field label="Sumber" name="sumber" />
                </div>
            </div>
        </x-fragments.form-modal>

        @foreach ($bacaan->details as $detail)
            <x-fragments.form-modal id="edit-detail-modal-{{ $detail->id }}" title="Edit Detail Bacaan"
                action="{{ route('symuslim.update-detail', [$bacaan->id, $detail->id]) }}" method="PUT">
                <div class="grid grid-cols-2 gap-2">
                    <div class="col-span-2">
                        <x-fragments.textarea-field label="Arab" name="arab" :value="$detail->arab" rows="3" />
                        <x-fragments.textarea-field label="Latin" name="latin" :value="$detail->latin" rows="3" />
                        <x-fragments.textarea-field label="Terjemahan" name="terjemahan"
                            :value="$detail->terjemahan" rows="3" />
                        <x-fragments.text-field label="Sumber" name="sumber" :value="$detail->sumber" />
                    </div>
                </div>
            </x-fragments.form-modal>
        @endforeach
    @endforeach

    <div class="p-3 rounded-lg bg-white/25 shadow-lg backdrop-blur-3xl" x-data="{ filter: 'all' }">

        <!-- Header: Filter + Search + Add Button -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">

            <!-- Filter Buttons -->
            <div class="flex space-x-2">
                <a href="{{ route('symuslim.index') }}"
                    class="px-3 py-1 rounded-lg text-sm font-medium {{ request('type') ? 'bg-gray-200 text-gray-700' : 'bg-indigo-600 text-white' }}">
                    Semua
                </a>
                <a href="{{ route('symuslim.index', ['type' => 'doa']) }}"
                    class="px-3 py-1 rounded-lg text-sm font-medium {{ request('type') == 'doa' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Doa
                </a>
                <a href="{{ route('symuslim.index', ['type' => 'hadits']) }}"
                    class="px-3 py-1 rounded-lg text-sm font-medium {{ request('type') == 'hadits' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Hadits
                </a>
                <a href="{{ route('symuslim.index', ['type' => 'dzikir']) }}"
                    class="px-3 py-1 rounded-lg text-sm font-medium {{ request('type') == 'dzikir' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Dzikir
                </a>
            </div>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('symuslim.index') }}" class="flex items-center space-x-2">
                @if (request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <input type="text" name="search" placeholder="Cari judul/deskripsi..."
                    value="{{ request('search') }}"
                    class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit"
                    class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                    Cari
                </button>
            </form>

            <!-- Add Bacaan Button -->
            <x-fragments.modal-button target="add-bacaan-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Bacaan
            </x-fragments.modal-button>
        </div>

        <!-- Data Bacaan -->
        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-4">Data Bacaan</h2>

            @forelse ($bacaans as $bacaan)
                <div class="border border-gray-200 rounded-lg bg-white/25 backdrop-blur-2xl shadow-sm"
                    x-data="{ isOpen: false }"
                    x-show="filter === 'all' || filter === '{{ $bacaan->type }}'"
                    x-transition>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead class="bg-gray-50/40">
                                <tr>
                                    <th class="px-4 py-3"></th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Detail</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-gray-50 transition-colors cursor-pointer">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div
                                            class="rounded-full w-7 h-7 transition-all flex text-center hover:bg-gray-300/45 justify-center items-center">
                                            <svg @click="isOpen = !isOpen"
                                                class="w-5 h-5 text-gray-400 transition-transform cursor-pointer"
                                                :class="{ 'rotate-180': isOpen }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 line-clamp-2">
                                        {{ $bacaan->judul }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($bacaan->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 line-clamp-2">
                                        {{ $bacaan->deskripsi ?? 'Tidak ada' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $bacaan->details->count() }} Detail
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <x-action-buttons modalId="edit-bacaan-modal-{{ $bacaan->id }}"
                                            deleteRoute="{{ route('symuslim.destroy', $bacaan->id) }}" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Detail Bacaan -->
                    <div x-show="isOpen" class="overflow-auto" x-transition>
                        <div class="border-t border-gray-200 p-4 bg-gray-50/30">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-medium text-gray-900 flex items-center">
                                    <i class="fa-solid fa-book-quran mr-2 text-indigo-600"></i>
                                    Detail Bacaan
                                </h4>
                                <x-fragments.modal-button target="add-detail-modal-{{ $bacaan->id }}" variant="indigo">
                                    <i class="fa-solid fa-plus mr-2"></i>
                                    Tambah Detail
                                </x-fragments.modal-button>
                            </div>

                            @if ($bacaan->details->count() > 0)
                                <div class="overflow-x-auto w-full">
                                    <table
                                        class="min-w-full bg-white/20 backdrop-blur-3xl border border-gray-200 rounded-lg table-fixed">
                                        <thead class="bg-gray-50/50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Arab</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Latin</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Terjemahan</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Sumber</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach ($bacaan->details as $detail)
                                                <tr class="hover:bg-gray-50/30 transition-colors">
                                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $detail->arab ?? '-' }}</td>
                                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $detail->latin ?? '-' }}</td>
                                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $detail->terjemahan ?? '-' }}</td>
                                                    <td class="px-4 py-4 text-sm text-gray-900">{{ $detail->sumber ?? '-' }}</td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <x-action-buttons modalId="edit-detail-modal-{{ $detail->id }}"
                                                            deleteRoute="{{ route('symuslim.destroy-detail', [$bacaan->id, $detail->id]) }}" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fa-solid fa-book-open text-3xl mb-3 text-gray-300"></i>
                                    <p class="text-base font-medium">Belum ada detail</p>
                                    <p class="text-sm text-gray-400">Mulai dengan menambahkan detail baru</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fa-solid fa-book-quran text-5xl mb-4 text-gray-300"></i>
                    <p class="text-xl font-medium">Belum ada data bacaan</p>
                    <p class="text-sm text-gray-400 mt-1">Mulai dengan menambahkan bacaan baru</p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-4">
                {{ $bacaans->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
