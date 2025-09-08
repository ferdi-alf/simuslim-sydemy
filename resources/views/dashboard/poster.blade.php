@extends('layouts.dashboard-layouts')
@section('title', 'Poster Dakwah')

@section('content')
{{-- Modal Tambah Poster --}}
<x-fragments.form-modal id="add-poster-modal" title="Tambah Poster" action="{{ route('poster.store') }}">
    <div x-data="fileUpload()"
        class="border-2 border-dashed border-gray-300 rounded-lg p-5 text-center hover:border-blue-400 transition-colors"
        @dragover.prevent @drop.prevent="handleDrop($event)" 
        @dragenter.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false" 
        :class="{ 'border-blue-400 bg-blue-50': isDragging }">

        {{-- Saat belum ada file --}}
        <div x-show="!selectedFile">
            <p class="text-sm text-gray-600">Drop file poster di sini atau</p>
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

        <input type="file" x-ref="fileInput" name="poster" accept="image/*" 
            class="hidden" @change="handleFileSelect($event)" required>
    </div>

    <x-fragments.text-field label="Judul" name="judul" required />
</x-fragments.form-modal>

{{-- Container --}}
<div class="p-3 rounded-lg bg-white/45 shadow-lg backdrop-blur-3xl">
    <div class="flex justify-end mb-4">
        <x-fragments.modal-button target="add-poster-modal" variant="indigo">
            <i class="fa-solid fa-plus mr-2"></i>
            Tambah Poster
        </x-fragments.modal-button>
    </div>

    <div class="mt-6">
        <h2 class="text-lg font-semibold mb-2">Data Poster Dakwah</h2>
        <x-reusable-table
            :headers="['No', 'Judul', 'Poster']"
            :data="$data"
            :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->judul,
                fn($row) => $row->poster_html,
            ]"
            :showActions="true"
            :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-update-poster-' . $row->id,
                'updateRoute' => route('poster.update', $row->id),
                'deleteRoute' => route('poster.destroy', $row->id),
            ])" />
    </div>
</div>

{{-- Loop Poster --}}
@foreach ($data as $poster)

    {{-- Modal Update Poster --}}
    <x-fragments.form-modal id="modal-update-poster-{{ $poster->id }}" title="Edit Poster"
        action="{{ route('poster.update', $poster->id) }}" method="PUT">
        <x-fragments.text-field label="Judul" name="judul" :value="$poster->judul" required />
        <div class="mt-3">
            <p class="text-sm">Poster saat ini:</p>
            <img src="{{ asset('uploads/posters/' . $poster->poster) }}" 
                class="w-40 rounded-md mt-2 mb-3 cursor-pointer"
                @click="$dispatch('open-modal', { id: 'poster-modal-{{ $poster->id }}' })">
        </div>
        <input type="file" name="poster" accept="image/*">
    </x-fragments.form-modal>

    {{-- Modal Preview Poster --}}
    <x-modal-layout id="poster-modal-{{ $poster->id }}" title="Poster: {{ $poster->judul }}">
        <div class="text-center w-full">
            <img src="{{ asset('uploads/posters/' . $poster->poster) }}"
                class="w-full max-h-96 object-contain mx-auto rounded-lg shadow-lg"
                alt="{{ $poster->judul }}">
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-1">{{ $poster->judul }}</h5>
                <p class="text-sm text-gray-500">Poster Dakwah</p>
            </div>
        </div>
    </x-modal-layout>
@endforeach

{{-- Script Upload --}}
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
