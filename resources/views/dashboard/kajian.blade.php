@extends('layouts.dashboard-layouts')
@section('title', 'Kajian Page')

@section('content')
<x-fragments.form-modal id="add-kajian-modal" title="Tambah Kajian" action="{{ route('kajian.store') }}">
    <div class="grid grid-cols-2 gap-2 overflow-auto h-96">
        <div class="col-span-2 ">
            <label class="block text-sm font-medium text-gray-700 ">
                Poster Kajian <span class="text-red-500">*</span>
            </label>

            <div x-data="fileUpload()"
                class="border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-400 transition-colors"
                @dragover.prevent @drop.prevent="handleDrop($event)" @dragenter.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false" :class="{ 'border-blue-400 bg-blue-50': isDragging }">

                <div x-show="!selectedFile">
                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none"
                        viewBox="0 0 48 48">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">Drop file poster di sini atau</p>
                        <button type="button" @click="$refs.fileInput.click()"
                            class="mt-2 text-blue-600 hover:text-blue-500 font-medium">
                            klik untuk memilih file
                        </button>
                    </div>
                </div>

                <div x-show="selectedFile" class="text-left">
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900"
                                    x-text="selectedFile ? selectedFile.name : ''"></p>
                                <p class="text-xs text-gray-500"
                                    x-text="selectedFile ? formatFileSize(selectedFile.size) : ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="removeFile()" class="text-red-500 hover:text-red-700">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <input type="file" x-ref="fileInput" name="poster" accept="image/*" class="hidden"
                    @change="handleFileSelect($event)">
            </div>
        </div>
        <x-fragments.text-field label="Judul Kajian" name="judul" required />
        <x-fragments.text-field label="Penyelenggara" name="penyelenggara" required />
        <x-fragments.multiple-select label="Kategori" name="category_ids" :options="$categoriesOptions"
            placeholder="Pilih atau ketik Kategori baru" required />
        <x-fragments.select-field label="Jenis Kajian" name="jenis" :options="['rutin' => 'Rutin', 'akbar/dauroh' => 'Akbar/Dauroh']" />
        <div class="col-span-2">
            <label for="keterangan">
                Masukan keterangan
            </label>
            <textarea id="keterangan" name="keterangan" rows="10"
                class="w-full border 2 border-gray-300 rounded-md px-3 py-2 h-28 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan Keterangan kajian"></textarea>
        </div>

        <div class="col-span-2 ">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Lokasi Kajian <span class="text-red-500">*</span>
            </label>

            <div x-data="{ lokasiType: 'masjid' }" class="space-y-3">
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" x-model="lokasiType" value="masjid" class="mr-2">
                        <span>Di Masjid</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" x-model="lokasiType" value="manual" class="mr-2">
                        <span>Alamat Manual</span>
                    </label>
                </div>

                <div x-show="lokasiType === 'masjid'" x-transition>
                    <select name="masjid_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Masjid</option>
                        @foreach ($masjids as $masjid)
                        <option class="w-full overflow-hidden" value="{{ $masjid->id }}">{{ $masjid->nama }}
                            {{ $masjid->alamat }}
                        </option>
                        @endforeach
                    </select>

                </div>
                <div x-show="lokasiType === 'manual'" x-transition>
                    <textarea name="alamat_manual" rows="2"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Masukkan alamat lengkap lokasi kajian"></textarea>
                </div>
            </div>
        </div>
    </div>
</x-fragments.form-modal>

@foreach($kajians as $kajian)
<x-fragments.form-modal id="modal-edit-kajian-{{ $kajian->id }}" title="Edit Kajian"
    action="{{ route('kajian.update', $kajian->id) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    <div class="grid grid-cols-2 gap-2 overflow-auto h-96">
        <div class="col-span-2">
            {{-- Upload Poster --}}
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Poster Kajian <span class="text-red-500">*</span>
            </label>
            <div x-data="fileUpload('{{ $kajian->poster ? asset('uploads/kajian-poster/' . $kajian->poster) : '' }}')" x-init="init()"
                class="border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-400 transition-colors"
                @dragover.prevent @drop.prevent="handleDrop($event)" @dragenter.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false" :class="{ 'border-blue-400 bg-blue-50': isDragging }">

                <div x-show="!selectedFile && !previewImage">
                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none"
                        viewBox="0 0 48 48">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Drop file poster di sini atau</p>
                        <button type="button" @click="$refs.fileInput.click()"
                            class="mt-2 text-blue-600 hover:text-blue-500 font-medium">
                            klik untuk memilih file
                        </button>
                    </div>
                </div>
                <div x-show="selectedFile || previewImage" class="text-left">
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
                        <div class="flex items-center">
                            <img x-bind:src="previewImage" class="h-16 w-16 object-cover rounded mr-3"
                                alt="Poster Preview" />
                            <div>
                                <p class="text-sm font-medium text-gray-900"
                                    x-text="selectedFile 
                                                ? selectedFile.name 
                                                : (existingPoster ? existingPoster.split('/').pop() : 'No poster')">
                                </p>
                                <p class="text-xs text-gray-500"
                                    x-text="selectedFile ? formatFileSize(selectedFile.size) : ''"></p>
                            </div>
                        </div>

                        <button type="button" @click="removeFile()" class="text-red-500 hover:text-red-700">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <input type="file" x-ref="fileInput" name="poster" accept="image/*" class="hidden"
                    @change="handleFileSelect($event)">
                <input type="hidden" name="delete_poster" x-bind:value="deletePoster ? 1 : 0">
            </div>
        </div>

        <x-fragments.text-field label="Judul Kajian" name="judul" required :value="$kajian->judul" />
        <x-fragments.text-field label="Penyelenggara" name="penyelenggara" required :value="$kajian->penyelenggara" />
        <x-fragments.multiple-select label="Kategori" name="category_ids" :options="$categoriesOptions" :value="$kajian->categories->pluck('id')->toArray()"
            placeholder="Pilih atau ketik Kategori baru" required />
        <x-fragments.select-field label="Jenis Kajian" name="jenis" :options="['rutin' => 'Rutin', 'akbar/dauroh' => 'Akbar/Dauroh']" :value="$kajian->jenis" />

        <div class="col-span-2">
            <label for="keterangan">
                Masukan keterangan
            </label>
            <textarea id="keterangan" name="keterangan" rows="10"
                class="w-full border 2 border-gray-300 rounded-md px-3 py-2 h-28 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Masukkan Keterangan kajian">{{ old('keterangan', $kajian->keterangan) }}</textarea>
        </div>

        <div class="col-span-2 ">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Lokasi Kajian <span class="text-red-500">*</span>
            </label>

            <div x-data="{ lokasiType: '{{ $kajian->masjid_id ? 'masjid' : 'manual' }}' }" class="space-y-3">
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" x-model="lokasiType" value="masjid" class="mr-2">
                        <span>Di Masjid</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" x-model="lokasiType" value="manual" class="mr-2">
                        <span>Alamat Manual</span>
                    </label>
                </div>

                <div x-show="lokasiType === 'masjid'" x-transition>
                    <select name="masjid_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Masjid</option>
                        @foreach ($masjids as $masjid)
                        <option value="{{ $masjid->id }}"
                            {{ old('masjid_id', $kajian->masjid_id) == $masjid->id ? 'selected' : '' }}>
                            {{ $masjid->nama }} - {{ $masjid->alamat }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div x-show="lokasiType === 'manual'" x-transition>
                    <textarea name="alamat_manual" rows="2"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Masukkan alamat lengkap lokasi kajian">{{ old('alamat_manual', $kajian->alamat_manual) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</x-fragments.form-modal>

<x-fragments.form-modal id="add-jadwal-modal-{{ $kajian->id }}" title="Tambah Jadwal Kajian"
    action="{{ route('kajian.store-jadwal') }}">
    <input class="hidden" value="{{ $kajian->id }}" name="kajian_id" id="jadwal-kajian-id">

    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <x-fragments.text-field label="Tanggal" name="tanggal" type="date" required />
        </div>

        <div>
            <label for="time" class="block mb-2 text-sm font-medium text-gray-900">Jam Mulai:</label>
            <div class="relative">
                <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="time" id="time" name="jam_mulai"
                    class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required />
            </div>
        </div>

        <div>
            <label for="time" class="block mb-2 text-sm font-medium text-gray-900">Jam Selesai:</label>
            <div class="relative">
                <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="time" id="time" name="jam_selesai"
                    class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required />
            </div>
        </div>

        <x-fragments.select-field
            label="Status"
            name="status"
            :options="\App\Models\JadwalKajian::statusOptions()"
            :value="old('status', 'belum dimulai')" />

        <x-fragments.select-field label="Diperuntukan" name="diperuntukan" :options="[
                        'semua kaum muslim' => 'Semua Kaum Muslim',
                        'ikhwan' => 'Ikhwan',
                        'akhwat' => 'Akhwat',
                    ]" />

        {{-- link live --}}
        <div class="col-span-2">
            <x-fragments.text-field label="Link" name="link" placeholder="Masukkan link YouTube / lainnya"
                required />
        </div>

        <div class="md:col-span-2">
            <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions"
                placeholder="Pilih atau ketik nama ustadz baru" required />
        </div>
    </div>
</x-fragments.form-modal>

@foreach ($kajian->jadwalKajians as $jadwal)
<x-fragments.form-modal id="modal-edit-jadwal-{{ $jadwal->id }}" title="Edit Jadwal Kajian"
    action="{{ route('kajian.update-jadwal', $jadwal->id) }}" method="PUT">
    @csrf
    @method('PUT')
    <input class="hidden" value="{{ $kajian->id }}" name="kajian_id" id="jadwal-kajian-id">

    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <x-fragments.text-field label="Tanggal" name="tanggal" :value="$jadwal->tanggal" type="date"
                required />
        </div>

        <div>
            <label for="jam_mulai_{{ $jadwal->id }}"
                class="block mb-2 text-sm font-medium text-gray-900">Jam Mulai:</label>
            <div class="relative">
                <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="time" id="jam_mulai_{{ $jadwal->id }}" name="jam_mulai"
                    value="{{ $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '' }}"
                    step="60"
                    class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required />
            </div>
        </div>

        <div>
            <label for="jam_selesai_{{ $jadwal->id }}"
                class="block mb-2 text-sm font-medium text-gray-900">Jam Selesai:</label>
            <div class="relative">
                <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="time" id="jam_selesai_{{ $jadwal->id }}" name="jam_selesai"
                    value="{{ $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '' }}"
                    step="60"
                    class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required />
            </div>
        </div>

        <x-fragments.select-field
            label="Status"
            name="status"
            :options="\App\Models\JadwalKajian::statusOptions()"
            :value="$jadwal->status" />

        <x-fragments.select-field label="Diperuntukan" name="diperuntukan" :options="[
                            'semua kaum muslim' => 'Semua Kaum Muslim',
                            'ikhwan' => 'Ikhwan',
                            'akhwat' => 'Akhwat',
                        ]"
            :value="$jadwal->diperuntukan" />

        {{-- link live --}}
        <div class="col-span-2">
            <x-fragments.text-field label="Link" name="link"
                placeholder="Masukkan link YouTube / lainnya" :value="old('link', $jadwal->link)" required />

        </div>

        <div class="md:col-span-2">
            <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions"
                :value="$jadwal->ustadzs->pluck('id')->toArray()" placeholder="Pilih atau ketik nama ustadz baru" required />
        </div>
    </div>
</x-fragments.form-modal>
@endforeach
@endforeach

{{-- ini table utama kajian --}}
<div class="p-3 rounded-lg bg-white/25 shadow-lg backdrop-blur-3xl">
    <div class="flex justify-end mb-4">
        <x-fragments.modal-button target="add-kajian-modal" variant="indigo">
            <i class="fa-solid fa-plus mr-2"></i>
            Tambah Kajian
        </x-fragments.modal-button>
    </div>

    <div class="mt-6">
        <h2 class="text-lg font-semibold mb-4">Data Kajian</h2>

        {{-- Filter Baru --}}
        <div x-data="filterKajian()" class="mb-6 p-4 bg-white/30 rounded-lg shadow-sm border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                {{-- Filter berdasarkan Hari --}}
                <div>
                    <label for="filterHari" class="block text-sm font-medium text-gray-700">Filter berdasarkan Hari</label>
                    <select x-model="filterHari" id="filterHari" @change="applyFilters()"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Semua Hari</option>
                        <option value="Ahad">Ahad</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                    </select>
                </div>

                {{-- Filter berdasarkan Status --}}
                <div>
                    <label for="filterStatus" class="block text-sm font-medium text-gray-700">Filter berdasarkan Status</label>
                    <select x-model="filterStatus" id="filterStatus" @change="applyFilters()"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Semua Status</option>
                        <option value="belum dimulai">Belum Dimulai</option>
                        <option value="berjalan">Berjalan</option>
                        <option value="selesai">Selesai</option>
                        <option value="diliburkan">Diliburkan</option>
                    </select>
                </div>

                {{-- Tombol Reset --}}
                <div>
                    <button @click="resetFilters()" class="w-full md:w-auto px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-150 ease-in-out">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            @if($kajians->count() > 0)
            @foreach($kajians as $kajian)
            <div class="border border-gray-200 rounded-lg bg-white/25 backdrop-blur-2xl shadow-sm kajian-item"
                data-jadwals='@json($kajian->jadwalKajians->map(fn($jadwal) => [
                    'hari' => $jadwal->hari,
                    'status' => $jadwal->status
                ]))'

                x-data="{ isOpen: false }">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50/40">
                            <tr>
                                <th class="px-4 py-3"></th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Poster</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Judul</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Penyelenggara</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lokasi</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jadwal</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div
                                        class="rounded-full w-7 h-7 transition-all flex text-center hover:bg-gray-300/45 justify-center items-center">
                                        <svg @click="isOpen = !isOpen"
                                            class="w-5 h-5 text-gray-400 transition-transform cursor-pointer"
                                            :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="relative group">
                                            @if($kajian->poster)
                                            <img src="{{ asset('uploads/kajian-poster/' . $kajian->poster) }}"
                                                class="w-16 h-16 rounded-lg object-cover cursor-pointer transition-all duration-300 group-hover:ring-2 group-hover:ring-indigo-500"
                                                alt="{{ $kajian->judul }}"
                                                data-modal-target="poster-modal-{{ $kajian->id }}"
                                                data-modal-toggle="poster-modal-{{ $kajian->id }}">
                                            @else
                                            <span> - </span>
                                            @endif

                                            <!-- Tooltip -->
                                            <div
                                                class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-full 
                                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300 
                                                        bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                                Klik untuk melihat poster
                                                <div
                                                    class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-full 
                                                            border-4 border-transparent border-b-gray-900">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <span
                                            class="text-sm font-medium text-gray-900 line-clamp-2">{{ $kajian->judul }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($kajian->categories->count() > 0)
                                    <div class="grid gap-2 col-span-2">
                                        @foreach ($kajian->categories as $category)
                                        <span
                                            class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                            {{ $category->nama }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @else
                                    <span class="text-gray-400 italic text-sm">Belum ada
                                        Kategori</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $kajian->jenis === 'rutin' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($kajian->jenis) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $kajian->penyelenggara }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $kajian->lokasi }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $kajian->jadwalKajians->count() }} Jadwal
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <x-action-buttons modalId="modal-edit-kajian-{{ $kajian->id }}"
                                        deleteRoute="{{ route('kajian.destroy', $kajian->id) }}" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95">
                    <div class="border-t border-gray-200 p-4 bg-gray-50/30">
                        <div class="bg-gray-300/30 rounded-lg mx-10 my-2 p-3 border-l border-blue-500">
                            <p>{{ $kajian->keterangan }}</p>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-medium text-gray-900 flex items-center">
                                <i class="fa-solid fa-calendar-days mr-2 text-indigo-600"></i>
                                Jadwal Kajian
                            </h4>
                            <x-fragments.modal-button target="add-jadwal-modal-{{ $kajian->id }}"
                                variant="indigo">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Tambah Jadwal
                            </x-fragments.modal-button>
                        </div>

                        @if ($kajian->jadwalKajians->count() > 0)
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full bg-white/20 backdrop-blur-3xl border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fa-solid fa-calendar mr-1 text-indigo-500"></i>
                                            Hari & Tanggal
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fa-solid fa-clock mr-1 text-indigo-500"></i>
                                            Waktu
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fa-solid fa-users mr-1 text-green-500"></i>
                                            Target
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fa-solid fa-user-tie mr-1 text-purple-500"></i>
                                            Ustadz
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fa-solid fa-info-circle mr-1 text-blue-500"></i>
                                            Status
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <i class="fa-solid fa-cog mr-1"></i>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($kajian->jadwalKajians as $jadwal)
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <!-- Hari & Tanggal -->
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $jadwal->hari }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Waktu dengan format 24 jam -->
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-mono">
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                            </div>
                                        </td>

                                        <!-- Target Audience -->
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ ucfirst($jadwal->diperuntukan) }}
                                            </div>
                                        </td>

                                        <!-- Ustadz -->
                                        <td class="px-4 py-4">
                                            @if ($jadwal->ustadzs->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($jadwal->ustadzs as $ustadz)
                                                <span
                                                    class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                                    {{ $ustadz->nama_lengkap }}
                                                </span>
                                                @endforeach
                                            </div>
                                            @else
                                            <span class="text-gray-400 italic text-sm">Belum ada
                                                ustadz</span>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @php
                                            $statusClass = [
                                            'belum dimulai' => 'bg-gray-100 text-gray-800',
                                            'berjalan' => 'bg-blue-100 text-blue-800',
                                            'selesai' => 'bg-green-100 text-green-800',
                                            'dibatalkan' => 'bg-red-100 text-red-800'
                                            ][$jadwal->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst($jadwal->status) }}
                                            </span>
                                        </td>

                                        <!-- Action Buttons -->
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <x-action-buttons
                                                modalId="modal-edit-jadwal-{{ $jadwal->id }}"
                                                deleteRoute="{{ route('kajian.destroy-jadwal', $jadwal->id) }}" />
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fa-solid fa-calendar-xmark text-3xl mb-3 text-gray-300"></i>
                            <p class="text-base font-medium">Belum ada jadwal</p>
                            <p class="text-sm text-gray-400">Mulai dengan menambahkan jadwal baru untuk kajian
                                ini</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="fa-solid fa-mosque text-5xl mb-4 text-gray-300"></i>
                <p class="text-xl font-medium">Belum ada data kajian</p>
                <p class="text-sm text-gray-400 mt-1">Mulai dengan menambahkan kajian baru</p>
            </div>
            @endif

            <div id="no-results" class="text-center py-12 text-gray-500" style="display: none;">
                <i class="fa-solid fa-search text-5xl mb-4 text-gray-300"></i>
                <p class="text-xl font-medium">Tidak ada kajian yang cocok</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah atau reset filter Anda.</p>
            </div>
        </div>
    </div>
</div>

@foreach ($kajians as $kajian)
<x-modal-layout id="poster-modal-{{ $kajian->id }}" title="Poster Kajian: {{ $kajian->judul }}"
    :closable="true">
    <div class="text-center w-full">
        <img src="{{ asset('uploads/kajian-poster/' . $kajian->poster) }}"
            class="w-full max-h-96 object-contain mx-auto rounded-lg shadow-lg" alt="{{ $kajian->judul }}">

        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
            <h5 class="font-semibold text-gray-900 mb-1">{{ $kajian->judul }}</h5>
            <p class="text-sm text-gray-600">{{ $kajian->penyelenggara }}</p>
            <p class="text-sm text-gray-500">
                @if($kajian->categories->count() > 0)
                {{ $kajian->categories->pluck('nama')->implode(', ') }} •
                @endif
                {{ ucfirst($kajian->jenis) }}
            </p>
        </div>
    </div>
</x-modal-layout>
@endforeach

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        // FILE UPLOAD
        Alpine.data('fileUpload', (existingPoster = '') => ({
            isDragging: false,
            selectedFile: null,
            previewImage: null,
            existingPoster: existingPoster,
            deletePoster: false,

            init() {
                if (this.existingPoster) {
                    this.previewImage = this.existingPoster;
                }
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.selectedFile = file;
                    this.deletePoster = false;
                    const reader = new FileReader();
                    reader.onload = (e) => this.previewImage = e.target.result;
                    reader.readAsDataURL(file);
                }
            },

            handleDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.selectedFile = file;
                    this.deletePoster = false;
                    const reader = new FileReader();
                    reader.onload = (e) => this.previewImage = e.target.result;
                    reader.readAsDataURL(file);
                    this.isDragging = false;
                }
            },

            removeFile() {
                this.selectedFile = null;
                this.previewImage = null;
                this.deletePoster = true;
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }));

        // FILTER KAJIAN
        Alpine.data('filterKajian', () => ({
            filterHari: '',
            filterStatus: '',

            init() {
                // Inisialisasi filter dari URL jika ada
                const urlParams = new URLSearchParams(window.location.search);
                this.filterHari = urlParams.get('hari') || '';
                this.filterStatus = urlParams.get('status') || '';

                if (this.filterHari || this.filterStatus) {
                    this.applyFilters();
                }
            },

            applyFilters() {
                const hariValue = this.filterHari;
                const statusValue = this.filterStatus;
                let visibleCount = 0;

                // Update URL dengan parameter filter
                const urlParams = new URLSearchParams();
                if (hariValue) urlParams.set('hari', hariValue);
                if (statusValue) urlParams.set('status', statusValue);
                const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                window.history.replaceState({}, '', newUrl);

                // Iterasi melalui semua kajian
                document.querySelectorAll('.kajian-item').forEach(item => {
                    const jadwals = JSON.parse(item.getAttribute('data-jadwals'));
                    let shouldShow = true;

                    // Filter berdasarkan hari
                    if (hariValue && hariValue !== '') {
                        const hasHariMatch = jadwals.some(jadwal => jadwal.hari.toLowerCase() === hariValue.toLowerCase());
                        if (!hasHariMatch) shouldShow = false;
                    }

                    // Filter berdasarkan status
                    if (statusValue && statusValue !== '') {
                        const hasStatusMatch = jadwals.some(jadwal => jadwal.status.toLowerCase() === statusValue.toLowerCase());
                        if (!hasStatusMatch) shouldShow = false;
                    }

                    // Tampilkan atau sembunyikan berdasarkan hasil filter
                    if (shouldShow) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Tampilkan pesan jika tidak ada hasil
                const noResultsElement = document.getElementById('no-results');
                if (noResultsElement) {
                    noResultsElement.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            },

            resetFilters() {
                this.filterHari = '';
                this.filterStatus = '';

                // Reset URL
                window.history.replaceState({}, '', window.location.pathname);

                // Tampilkan semua kajian
                document.querySelectorAll('.kajian-item').forEach(item => {
                    item.style.display = 'block';
                });

                // Sembunyikan pesan no results
                const noResultsElement = document.getElementById('no-results');
                if (noResultsElement) {
                    noResultsElement.style.display = 'none';
                }
            }
        }));
    });

    // Fungsi untuk mengupdate status jadwal secara real-time
    function updateJadwalStatus() {
        document.querySelectorAll('.kajian-item').forEach(kajianItem => {
            const jadwals = JSON.parse(kajianItem.getAttribute('data-jadwals'));

            jadwals.forEach(jadwal => {
                // Logika untuk update status berdasarkan waktu
                // (bisa ditambahkan sesuai kebutuhan)
            });
        });
    }

    // Update status setiap menit
    setInterval(updateJadwalStatus, 60000);
    updateJadwalStatus(); // Jalankan sekali saat halaman dimuat
</script>

@endsection