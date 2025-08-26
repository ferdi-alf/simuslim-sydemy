@extends('layouts.dashboard-layouts')

@section('content')
    <x-fragments.form-modal id="add-ustadz-modal" title="Tambah Ustadz" action="{{ route('ustadz.store') }}">
        <x-fragments.text-field label="Nama Lengkap" name="nama_lengkap" required />
        <x-fragments.text-field label="Alamat" name="alamat" />
        <x-fragments.text-field label="Riwayat Pendidikan" name="riwayat_pendidikan" />
        <x-fragments.text-field label="Link Youtube" name="youtube" />
        <x-fragments.text-field label="Link Instagram" name="instagram" />
        <x-fragments.text-field label="Link Tiktok" name="tiktok" />
    </x-fragments.form-modal>
    <div>
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-ustadz-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Ustadz
            </x-fragments.modal-button>
        </div>


        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data Ustadz</h2>
            <x-reusable-table :headers="['No', 'Nama Lengkap', 'Alamat', 'Riwayat Pendidikan']" :data="$data" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->nama_lengkap,
                fn($row) => $row->alamat,
                fn($row) => $row->riwayat_pendidikan,
            ]" :showActions="true" :actionButtons="fn($row) => view('components.action-buttons', [
                'modalId' => 'modal-update-ustadz-' . $row->id,
                'updateRoute' => route('ustadz.update', $row->id),
                'deleteRoute' => route('ustadz.destroy', $row->id),
            ])" />
        </div>

    </div>
    @foreach ($data as $ustadz)
        <x-fragments.form-modal id="modal-update-ustadz-{{ $ustadz->id }}" title="Edit Ustadz"
            action="{{ route('ustadz.update', $ustadz->id) }}" method="PUT">
            <x-fragments.text-field label="Nama Lengkap" name="nama_lengkap" :value="$ustadz->nama_lengkap" required />
            <x-fragments.text-field label="Alamat" name="alamat" :value="$ustadz->alamat" />
            <x-fragments.text-field label="Riwayat Pendidikan" name="riwayat_pendidikan" :value="$ustadz->riwayat_pendidikan" />
            <x-fragments.text-field label="Link Youtube" name="youtube" :value="$ustadz->youtube" />
            <x-fragments.text-field label="Link Instagram" name="instagram" :value="$ustadz->instagram" />
            <x-fragments.text-field label="Link Tiktok" name="tiktok" :value="$ustadz->tiktok" />
        </x-fragments.form-modal>
    @endforeach
@endsection
