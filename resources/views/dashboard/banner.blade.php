@extends('layouts.dashboard-layouts')
@section('title', 'Banners Page')

@section('content')
<x-fragments.form-modal id="add-banner-modal" title="Tambah Banner" action="{{ route('banner.store') }}">
    <!-- Upload Banner -->
    <div x-data="fileUpload()"
        class="border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-400 transition-colors"
        @dragover.prevent @drop.prevent="handleDrop($event)"
        @dragenter.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        :class="{ 'border-blue-400 bg-blue-50': isDragging }">

        {{-- Saat belum ada file --}}
        <div x-show="!selectedFile">
            <p class="text-sm text-gray-600">Drop file Banner di sini atau</p>
            <button type="button" @click="$refs.fileInput.click()" class="mt-2 text-blue-600 hover:text-blue-500 font-medium">
                klik untuk memilih file
            </button>
        </div>

        {{-- Saat ada file terpilih --}}
        <div x-show="selectedFile" class="mt-3">
            <p class="text-sm font-medium" x-text="selectedFile ? selectedFile.name : ''"></p>
            <p class="text-xs text-gray-500" x-text="selectedFile ? formatFileSize(selectedFile.size) : ''"></p>
            <button type="button" @click="removeFile()" class="text-red-500 mt-2">Hapus</button>
        </div>

        <input type="file" x-ref="fileInput" name="banners" accept="image/*"
            class="hidden" @change="handleFileSelect($event)" required>
    </div>

    <!-- Input Judul -->
    <x-fragments.text-field label="Judul" name="judul" required />

    <!-- Input Kategori -->
    <x-fragments.select-field label="Kategori" name="kategori" :options="[
        'kajian akbar/dauroh' => 'Kajian Akbar/Dauroh',
        'kajian rutin' => 'Kajian Rutin',
        'event' => 'Event',
        'promosi' => 'Promosi',
        'poster islami' => 'Poster Islami',
        'social' => 'Social',
        'donasi' => 'Donasi',
    ]" required />

    <!-- Input Deskripsi -->
    <div class="mt-4">
        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            placeholder="Tuliskan deskripsi banner..."></textarea>
    </div>
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
        <x-reusable-table
            :headers="['No', 'Judul', 'Kategori', 'Deskripsi', 'Banner']"
            :data="$data"
            :columns="[
        fn($row, $i) => $i + 1,
        fn($row) => $row->judul,
        fn($row) => $row->kategori,
        fn($row) => $row->short_deskripsi,  // pake accessor
        fn($row) => $row->banner_html,      // accessor dari model
    ]"
            :isHtml="true"
            :showActions="true"
            :actionButtons="fn($row) => view('components.action-buttons', [
        'modalId' => 'modal-update-banner-' . $row->id,
        'updateRoute' => route('banner.update', $row->id),
        'deleteRoute' => route('banner.destroy', $row->id),
    ])" />


    </div>

</div>

@foreach ($data as $banner)
<x-fragments.form-modal id="modal-update-banner-{{ $banner->id }}" title="Edit Banner"
    action="{{ route('banner.update', $banner->id) }}" method="PUT">

    <!-- Upload Banner -->
    <div x-data="fileUpload()" ...>
        <!-- kode upload file tetap -->
    </div>

    <!-- Input Judul -->
    <x-fragments.text-field label="Judul" name="judul" :value="$banner->judul" required />

    <!-- Input Kategori -->
    <x-fragments.select-field label="Kategori" name="kategori" :options="[
        'kajian akbar/dauroh' => 'Kajian Akbar/Dauroh',
        'kajian rutin' => 'Kajian Rutin',
        'event' => 'Event',
        'promosi' => 'Promosi',
        'poster islami' => 'Poster Islami',
        'social' => 'Social',
        'donasi' => 'Donasi',
    ]" :value="$banner->kategori" required />

    <!-- Input Deskripsi -->
    <div class="mt-4">
        <label for="deskripsi-{{ $banner->id }}" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea id="deskripsi-{{ $banner->id }}" name="deskripsi" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            placeholder="Tuliskan deskripsi banner...">{{ $banner->deskripsi }}</textarea>
    </div>
</x-fragments.form-modal>


<x-modal-layout id="poster-modal-{{ $banner->id }}" title="Banner: {{ $banner->judul }}" :closable="true">
    <div class="text-center w-full">
        <img src="{{ asset('uploads/banners/' . $banner->banners) }}"
            class="w-full max-h-96 object-contain mx-auto rounded-lg shadow-lg" alt="{{ $banner->judul }}">

        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-left">
            <h5 class="font-semibold text-gray-900 mb-1">{{ $banner->judul }}</h5>
            <p class="text-sm text-gray-500 mb-3">{{ $banner->kategori }}</p>

            <div class="text-sm text-gray-700 whitespace-pre-line">
                {{ $banner->deskripsi }}
            </div>
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