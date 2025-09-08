@extends('layouts.dashboard-layouts')
@section('title', 'Rekaman Kajian Page')

@section('content')
    <x-fragments.form-modal id="add-kajian-rekaman-modal" title="Tambah Kajian Rekaman"
        action="{{ route('kajian-rekaman.store') }}">
        <x-fragments.text-field label="Judul" name="judul" required />
        <x-fragments.text-field label="Kitab" name="kitab" />
        <x-fragments.select-field label="Kategori" name="kategori" :options="[['value' => 'video', 'label' => 'Video'], ['value' => 'audio', 'label' => 'Audio']]" required />
        <x-fragments.text-field label="Link" name="link" required />
        <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions"
            placeholder="Pilih atau ketik nama ustadz baru" required />
    </x-fragments.form-modal>

    <div class="p-3 rounded-lg bg-white/45 shadow-lg backdrop-blur-3xl">
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-kajian-rekaman-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Kajian Rekaman
            </x-fragments.modal-button>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data Kajian Rekaman</h2>
            <x-reusable-table :headers="['No', 'Judul', 'Kitab', 'Kategori', 'Link', 'Ustadz']" :data="$data" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->judul,
                fn($row) => $row->kitab ?? '-',
                fn($row) => ucfirst($row->kategori),
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
            <x-fragments.text-field label="Judul" name="judul" :value="$kajianRekaman->judul" required />
            <x-fragments.text-field label="Kitab" name="kitab" :value="$kajianRekaman->kitab" />
            <x-fragments.select-field label="Kategori" name="kategori" :options="[['value' => 'video', 'label' => 'Video'], ['value' => 'audio', 'label' => 'Audio']]" :value="$kajianRekaman->kategori" required />
            <x-fragments.text-field label="Link" name="link" :value="$kajianRekaman->link" required />
            <x-fragments.multiple-select label="Ustadz" name="ustadz_ids" :options="$ustadzOptions" :value="$kajianRekaman->ustadzs->pluck('id')->toArray()"
                placeholder="Pilih atau ketik nama ustadz baru" required />
        </x-fragments.form-modal>
    @endforeach
@endsection
