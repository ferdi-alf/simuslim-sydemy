@extends('layouts.dashboard-layouts')

@section('content')
    <x-fragments.form-modal id="add-donasi-modal" title="Tambah Donasi" action="{{ route('donasi.store') }}">
        <div class="grid grid-cols-2 space-x-2">
            <div class="col-span-2">
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
                                        x-text="selectedFile ? formatFileSize(selectedFile.size) : ''">
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
                    <input type="file" x-ref="fileInput" name="poster" accept="image/*" class="hidden"
                        @change="handleFileSelect($event)" required>
                </div>
            </div>
            <x-fragments.text-field label="Judul" name="judul" required />
            <x-fragments.text-field label="Nama PIC" name="nama_pic" required />
            <x-fragments.text-field label="Keperluan" name="keperluan" required />
            <x-fragments.currency-field label="Nominal" name="nominal" />
            <x-fragments.text-field label="No Rekening" name="no_rekening" required />
            <x-fragments.text-field label="bank" name="bank" placeholder="Mandiri, BCA, BRI, DLL" required />
            <div class="col-span-2">
                <x-fragments.text-field label="Nama Pemilik Rekening" name="nama_pemilik_rekening" required />
            </div>
            <div class="col-span-2">
                <textarea name="keterangan" rows="2"
                    class="w-full border mt-2 border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan keterangan donasi"></textarea>
            </div>
        </div>
    </x-fragments.form-modal>

    <div class="p-3 rounded-lg bg-white/45 shadow-lg backdrop-blur-3xl">
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-donasi-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Donasi
            </x-fragments.modal-button>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data Donasi</h2>
            <x-reusable-table :headers="[
                'No',
                'Poster',
                'Judul',
                'Nama PIC',
                'Keperluan',
                'Nominal',
                'No Rekening',
                'bank',
                'nama pemilik',
                'Keterangan',
            ]" :data="$data" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->poster_html,
                fn($row) => $row->judul,
                fn($row) => $row->nama_pic,
                fn($row) => $row->keperluan,
                fn($row) => 'Rp ' . number_format($row->nominal, 0, ',', '.'),
                fn($row) => $row->no_rekening,
                fn($row) => $row->bank,
                fn($row) => $row->nama_pemilik_rekening,
                fn($row) => $row->keterangan,
            ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-update-donasi-' . $row->id,
                'updateRoute' => route('donasi.update', $row->id),
                'deleteRoute' => route('donasi.destroy', $row->id),
            ])" />
        </div>
    </div>

    @foreach ($data as $donasi)
        <x-fragments.form-modal id="modal-update-donasi-{{ $donasi->id }}" title="Edit Donasi"
            action="{{ route('donasi.update', $donasi->id) }}" method="PUT">
            <div class="grid grid-cols-2 gap-x-2">
                <div class="col-span-2">
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
                                            x-text="selectedFile ? selectedFile.name : ''">
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
                    </div>
                </div>
                <x-fragments.text-field label="Judul" name="judul" :value="$donasi->judul" required />
                <x-fragments.text-field label="Nama PIC" name="nama_pic" :value="$donasi->nama_pic" required />
                <x-fragments.text-field label="Keperluan" name="keperluan" :value="$donasi->keperluan" required />
                <x-fragments.currency-field label="Nominal" name="nominal" :value="number_format($donasi->nominal, 0, '', '')" />
                <x-fragments.text-field label="No Rekening" name="no_rekening" :value="$donasi->no_rekening" required />
                <x-fragments.text-field label="bank" name="bank" :value="$donasi->bank"
                    placeholder="Mandiri, BCA, BRI, DLL" required />
                <div class="col-span-2">
                    <x-fragments.text-field label="Nama Pemilik Rekening" :value="$donasi->nama_pemilik_rekening" name="nama_pemilik_rekening"
                        required />
                </div>
                <div class="col-span-2">
                    <textarea name="keterangan" rows="2"
                        class="w-full border mt-2 border-gray-300 rounded-md px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Masukkan keterangan donasi">{{ $donasi->keterangan }}</textarea>
                </div>
            </div>
        </x-fragments.form-modal>

        <x-modal-layout id="poster-modal-{{ $donasi->id }}" title="Poster Donasi: {{ $donasi->judul }}"
            :closable="true">
            <div class="text-center w-full">
                <img src="{{ asset('uploads/poster/' . $donasi->poster) }}"
                    class="w-full max-h-96 object-contain mx-auto rounded-lg shadow-lg" alt="{{ $donasi->judul }}">

                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <h5 class="font-semibold text-gray-900 mb-1">{{ $donasi->judul }}</h5>
                    <p class="text-sm text-gray-500">{{ $donasi->nama_pic }} • {{ $donasi->keperluan }}
                    </p>
                </div>
            </div>
        </x-modal-layout>
    @endforeach

    <script>
        function fileUpload() {
            return {
                selectedFile: null,
                isDragging: false,
                handleFileSelect(event) {
                    this.selectedFile = event.target.files[0];
                },
                handleDrop(event) {
                    this.selectedFile = event.dataTransfer.files[0];
                    this.isDragging = false;
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
