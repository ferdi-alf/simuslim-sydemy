@extends('layouts.dashboard-layouts')
@section('title', 'Banners Page')

@section('content')
    <x-fragments.form-modal id="add-banner-modal" title="Tambah Banner" action="{{ route('banner.store') }}">
        <div x-data="fileUpload()"
            class="border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-400 transition-colors"
            @dragover.prevent @drop.prevent="handleDrop($event)" @dragenter.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false" :class="{ 'border-blue-400 bg-blue-50': isDragging }">
            <div x-show="!selectedFile">
                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Drop file banner di sini atau</p>
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
                            <p class="text-sm font-medium text-gray-900" x-text="selectedFile ? selectedFile.name : ''"></p>
                            <p class="text-xs text-gray-500" x-text="selectedFile ? formatFileSize(selectedFile.size) : ''">
                            </p>
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
            <input type="file" x-ref="fileInput" name="banners" accept="image/*" class="hidden"
                @change="handleFileSelect($event)" required>
        </div>
        <x-fragments.text-field label="Judul" name="judul" required />
        <x-fragments.select-field label="Kategori" name="kategori" :options="[
            'kajian akbar/dauroh' => 'Kajian Akbar/Dauroh',
            'kajian rutin' => 'Kajian Rutin',
            'event' => 'Event',
            'promosi' => 'Promosi',
            'poster islami' => 'Poster Islami',
            'social' => 'Social',
            'donasi' => 'Donasi',
        ]" required />

    </x-fragments.form-modal>

    <div class="p-3 rounded-lg bg-white/45 shadow-lg backdrop-blur-3xl">
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-banner-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Banner
            </x-fragments.modal-button>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data Banner</h2>
            <x-reusable-table :headers="['No', 'Judul', 'Kategori', 'Banner']" :data="$data" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->judul,
                fn($row) => $row->kategori,
                fn($row) => $row->banner_html,
            ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-update-banner-' . $row->id,
                'updateRoute' => route('banner.update', $row->id),
                'deleteRoute' => route('banner.destroy', $row->id),
            ])" />
        </div>
    </div>

    @foreach ($data as $banner)
        <x-fragments.form-modal id="modal-update-banner-{{ $banner->id }}" title="Edit Banner"
            action="{{ route('banner.update', $banner->id) }}" method="PUT">
            <div x-data="fileUpload()"
                class="border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-400 transition-colors"
                @dragover.prevent @drop.prevent="handleDrop($event)" @dragenter.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false" :class="{ 'border-blue-400 bg-blue-50': isDragging }">
                <div x-show="!selectedFile">
                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Drop file banner di sini atau</p>
                        <button type="button" @click="$refs.fileInput.click()"
                            class="mt-2 text-blue-600 hover:text-blue-500 font-medium">
                            klik untuk memilih file
                        </button>
                    </div>
                </div>
                <div x-show="selectedFile" class="text-left">
                    <div class="flex items-center justify-between bg-gray-50/30 p-3 rounded-md">
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="selectedFile ? selectedFile.name : ''">
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
                <input type="file" x-ref="fileInput" name="banners" accept="image/*" class="hidden"
                    @change="handleFileSelect($event)">
            </div>
            <x-fragments.text-field label="Judul" name="judul" :value="$banner->judul" required />
            <x-fragments.select-field label="Kategori" name="kategori" :options="[
                'kajian akbar/dauroh' => 'Kajian Akbar/Dauroh',
                'kajian rutin' => 'Kajian Rutin',
                'event' => 'Event',
                'promosi' => 'Promosi',
                'poster islami' => 'Poster Islami',
                'social' => 'Social',
                'donasi' => 'Donasi',
            ]" :value="$banner->kategori" required />

        </x-fragments.form-modal>

        <x-modal-layout id="poster-modal-{{ $banner->id }}" title="Banner: {{ $banner->judul }}" :closable="true">
            <div class="text-center w-full">
                <img src="{{ asset('uploads/banners/' . $banner->banners) }}"
                    class="w-full max-h-96 object-contain mx-auto rounded-lg shadow-lg" alt="{{ $banner->judul }}">

                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <h5 class="font-semibold text-gray-900 mb-1">{{ $banner->judul }}</h5>
                    <p class="text-sm text-gray-500">{{ $banner->kategori }} • {{ ucfirst($banner->jenis) }}
                    </p>
                </div>
            </div>
        </x-modal-layout>
    @endforeach

    <script>
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
    </script>
@endsection
