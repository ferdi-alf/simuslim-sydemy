@extends('layouts.dashboard-layouts')
@section('title', 'Masjid Page')

@section('content')
    <x-fragments.form-modal id="add-masjid-modal" title="Tambah masjid" action="{{ route('masjid.store') }}">
        <x-fragments.text-field label="nama" name="nama" required />
        <x-fragments.text-field label="alamat" name="alamat" required />
        <x-fragments.text-field label="maps" name="maps" required />
    </x-fragments.form-modal>
    <div class="p-3 rounded-lg bg-white/45 shadow-lg backdrop-blur-3xl ">
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-masjid-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Masjid
            </x-fragments.modal-button>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data Admin</h2>
            <x-reusable-table :headers="['No', 'Nama', 'Name', 'maps']" :data="$data" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->nama,
                fn($row) => $row->alamat,
                fn($row) => $row->maps,
            ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-update-masjid-' . $row->id,
                'updateRoute' => route('masjid.update', $row->id),
                'deleteRoute' => route('masjid.destroy', $row->id),
            ])" />
        </div>
    </div>

    @foreach ($data as $masjid)
        @foreach ($data as $masjid)
            <x-fragments.form-modal id="modal-update-masjid-{{ $masjid->id }}" title="Edit masjid"
                action="{{ route('masjid.update', $masjid->id) }}" method="PUT">

                <x-fragments.text-field label="nama" name="nama" :value="$masjid->nama" required />
                <x-fragments.text-field label="alamat" name="alamat" :value="$masjid->alamat" required />
                <x-fragments.text-field label="maps" name="maps" :value="$masjid->maps" required />
            </x-fragments.form-modal>
        @endforeach
    @endforeach
@endsection
