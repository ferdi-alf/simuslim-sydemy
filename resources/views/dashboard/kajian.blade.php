@extends('layouts.dashboard-layouts')

@section('content')
    <x-fragments.form-modal id="add-kajian-modal" title="Tambah Kajian" action="{{ route('kajian.store') }}">
        <div class="grid grid-cols-2 gap-2 ">
            <div class="col-span-2 ">
                <label class="block text-sm font-medium text-gray-700 mb-2">
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
                        <div class="mt-4">
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
                        @change="handleFileSelect($event)" required>
                </div>
            </div>
            <x-fragments.text-field label="Judul Kajian" name="judul" required />
            <x-fragments.text-field label="Penyelenggara" name="penyelenggara" required />
            <x-fragments.text-field label="Kategori" name="kategori" required placeholder="akidah, fiqih, tafsir, dll." />
            <x-fragments.select-field label="Jenis Kajian" name="jenis" :options="['rutin' => 'Rutin', 'akbar/dauroh' => 'Akbar/Dauroh']" />

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
    @forelse($kajians as $kajian)
        <x-fragments.form-modal id="modal-edit-kajian-{{ $kajian->id }}" title="Tambah Kajian"
            action="{{ route('kajian.store') }}">
            <div class="grid grid-cols-2 gap-2 ">
                <div class="col-span-2 ">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
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
                            <div class="mt-4">
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
                            @change="handleFileSelect($event)" required>
                    </div>
                </div>
                <x-fragments.text-field label="Judul Kajian" name="judul" required :value="$kajian->judul" />
                <x-fragments.text-field label="Penyelenggara" name="penyelenggara" required :value="$kajian->penyelenggara" />
                <x-fragments.text-field label="Kategori" name="kategori" required
                    placeholder="akidah, fiqih, tafsir, dll." :value="$kajian->kategori" />
                <x-fragments.select-field label="Jenis Kajian" name="jenis" :options="['rutin' => 'Rutin', 'akbar/dauroh' => 'Akbar/Dauroh']" :value="$kajian->jenis" />

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
                                    <option value="{{ $masjid->id }}"
                                        {{ old('masjid_id', isset($kajian) ? $kajian->masjid_id : '') == $masjid->id ? 'selected' : '' }}>
                                        {{ $masjid->nama }} - {{ $masjid->alamat }}
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



        <x-fragments.form-modal id="add-jadwal-modal-{{ $kajian->id }}" title="Tambah Jadwal Kajian"
            action="{{ route('kajian.store-jadwal') }}">
            <input class="hidden" value="{{ $kajian->id }}" name="kajian_id" id="jadwal-kajian-id">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <x-fragments.text-field label="Tanggal" name="tanggal" type="date" required />


                </div>

                <div class="">
                    <label for="time" class="block mb-2 text-sm font-medium text-gray-900 ">Jam Mulai:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="time" id="time" name="jam_mulai"
                            class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            required />
                    </div>
                </div>
                <div class="">
                    <label for="time" class="block mb-2 text-sm font-medium text-gray-900 ">Jam Selesai:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="time" id="time" name="jam_selesai"
                            class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            required />
                    </div>
                </div>

                <x-fragments.select-field label="Status" name="status" :options="[
                    'belum dimulai' => 'Belum Dimulai',
                    'berjalan' => 'berjalan',
                    'selesai' => 'Selesai',
                    'liburkan' => 'Liburkan',
                ]" />
                <x-fragments.select-field label="Diperuntukan" name="diperuntukan" :options="[
                    'semua kaum muslim' => 'Semua Kaum muslim',
                    'ikwhan' => 'ikwhan',
                    'akhwat' => 'Akhwat',
                ]" />

                <div class="md:col-span-2">
                    <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions"
                        placeholder="Pilih atau ketik nama ustadz baru" required />
                </div>
            </div>
        </x-fragments.form-modal>
        <!-- Modal Form - Fixed -->
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

                    <x-fragments.select-field label="Status" name="status" :options="[
                        'belum dimulai' => 'Belum Dimulai',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'liburkan' => 'Liburkan',
                    ]" :value="$jadwal->status" />

                    <x-fragments.select-field label="Diperuntukan" name="diperuntukan" :options="[
                        'semua kaum muslim' => 'Semua Kaum Muslim',
                        'ikhwan' => 'Ikhwan',
                        'akhwat' => 'Akhwat',
                    ]"
                        :value="$jadwal->diperuntukan" />

                    <div class="md:col-span-2">
                        <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions"
                            :value="$jadwal->ustadzs->pluck('id')->toArray()" placeholder="Pilih atau ketik nama ustadz baru" required />
                    </div>
                </div>
            </x-fragments.form-modal>
        @endforeach
    @endforeach



    <div class="p-3 rounded-lg bg-white/25 shadow-lg backdrop-blur-3xl">
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-kajian-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Kajian
            </x-fragments.modal-button>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-4">Data Kajian</h2>

            <div class="space-y-2">
                @forelse($kajians as $kajian)
                    <div class="border border-gray-200 rounded-lg bg-white/25 backdrop-blur-2xl shadow-sm"
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
                                                    <img src="{{ asset('uploads/poster/' . $kajian->poster) }}"
                                                        class="w-16 h-16 rounded-lg object-cover cursor-pointer transition-all duration-300 group-hover:ring-2 group-hover:ring-indigo-500"
                                                        alt="{{ $kajian->judul }}"
                                                        data-modal-target="poster-modal-{{ $kajian->id }}"
                                                        data-modal-toggle="poster-modal-{{ $kajian->id }}">

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
                                            <p class="text-xs text-gray-500">{{ $kajian->kategori }}</p>
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
                                                                    {{ $jadwal->hari }}</div>
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
                                                            <span
                                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $jadwal->status === 'belum dimulai'
                                            ? 'bg-gray-100 text-gray-800'
                                            : ($jadwal->status === 'berjalan'
                                                ? 'bg-blue-100 text-blue-800'
                                                : ($jadwal->status === 'selesai'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800')) }}">
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



                @empty
                    <div class="text-center py-12 text-gray-500">
                        <i class="fa-solid fa-mosque text-5xl mb-4 text-gray-300"></i>
                        <p class="text-xl font-medium">Belum ada data kajian</p>
                        <p class="text-sm text-gray-400 mt-1">Mulai dengan menambahkan kajian baru</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @foreach ($kajians as $kajian)
        {{-- Modal untuk Poster --}}
        <x-modal-layout id="poster-modal-{{ $kajian->id }}" title="Poster Kajian: {{ $kajian->judul }}"
            :closable="true">
            <div class="text-center w-full">
                <img src="{{ asset('uploads/poster/' . $kajian->poster) }}"
                    class="w-full max-h-96 object-contain mx-auto rounded-lg shadow-lg" alt="{{ $kajian->judul }}">

                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <h5 class="font-semibold text-gray-900 mb-1">{{ $kajian->judul }}</h5>
                    <p class="text-sm text-gray-600">{{ $kajian->penyelenggara }}</p>
                    <p class="text-sm text-gray-500">{{ $kajian->kategori }} • {{ ucfirst($kajian->jenis) }}
                    </p>
                </div>
            </div>
        </x-modal-layout>
    @endforeach

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // File upload handler
        function fileUpload() {
            return {
                isDragging: false,
                selectedFile: null,

                handleDrop(event) {
                    this.isDragging = false;
                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.selectedFile = files[0];
                        this.$refs.fileInput.files = files;
                    }
                },

                handleFileSelect(event) {
                    this.selectedFile = event.target.files[0];
                },

                removeFile() {
                    this.selectedFile = null;
                    this.$refs.fileInput.value = '';
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }
        }

        // Function to open add jadwal modal with kajian_id
        function openAddJadwalModal(kajianId) {
            document.getElementById('jadwal-kajian-id').value = kajianId;
            const modal = document.getElementById('add-jadwal-modal');
            const backdrop = modal.querySelector('.modal-backdrop') || modal;
            backdrop.classList.remove('hidden');
        }

        // Function to open edit jadwal modal
        function openEditJadwalModal(jadwalId, jadwalData) {
            // Set form action
            const form = document.querySelector('#edit-jadwal-modal form');
            form.action = `{{ url('kajian/jadwal') }}/${jadwalId}`;

            // Populate form fields
            document.querySelector('#edit-jadwal-modal input[name="tanggal"]').value = jadwalData.tanggal;
            document.querySelector('#edit-jadwal-modal select[name="hari"]').value = jadwalData.hari;
            document.querySelector('#edit-jadwal-modal input[name="jam_mulai"]').value = jadwalData.jam_mulai;
            document.querySelector('#edit-jadwal-modal input[name="jam_selesai"]').value = jadwalData.jam_selesai;
            document.querySelector('#edit-jadwal-modal select[name="status"]').value = jadwalData.status;
            document.querySelector('#edit-jadwal-modal select[name="diperuntukan"]').value = jadwalData.diperuntukan;

            // Open modal
            const modal = document.getElementById('edit-jadwal-modal');
            const backdrop = modal.querySelector('.modal-backdrop') || modal;
            backdrop.classList.remove('hidden');
        }
    </script>
@endsection
