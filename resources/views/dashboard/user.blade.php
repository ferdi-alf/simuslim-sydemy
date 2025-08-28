@extends('layouts.dashboard-layouts')

@section('content')
    <x-fragments.form-modal id="add-user-modal" title="Tambah User" action="{{ route('user.store') }}">
        <x-fragments.text-field label="Nama" name="name" required />
        <x-fragments.text-field label="Email" name="email" type="email" required />
        <x-fragments.text-field label="Password" name="password" type="password" required />
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role" id="role" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="jamaah">Jamaah</option>
            </select>
        </div>
    </x-fragments.form-modal>

    <div class="p-3 rounded-lg bg-white/45 shadow-lg backdrop-blur-3xl ">
        <div class="flex justify-end mb-4">
            <x-fragments.modal-button target="add-user-modal" variant="indigo">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah User
            </x-fragments.modal-button>
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Data User</h2>
            <x-reusable-table :headers="['No', 'Nama', 'Email', 'Role', 'Tanggal Daftar']" :data="$data" :columns="[
                fn($row, $i) => $i + 1,
                fn($row) => $row->name,
                fn($row) => $row->email,
                fn($row) => $row->role,
                fn($row) => $row->created_at->format('d/m/Y H:i'),
            ]" :showActions="true"
                :actionButtons="fn($row) => view('components.action-buttons', [
                                'modalId' => 'modal-update-user-' . $row->id,
                                'updateRoute' => route('user.update', $row->id),
                                'deleteRoute' => route('user.destroy', $row->id),
                            ])" />
        </div>
    </div>

    @foreach ($data as $user)
        <x-fragments.form-modal id="modal-update-user-{{ $user->id }}" title="Edit User"
            action="{{ route('user.update', $user->id) }}" method="PUT">

            <x-fragments.text-field label="Nama" name="name" :value="$user->name" required />
            <x-fragments.text-field label="Email" name="email" type="email" :value="$user->email" required />
            <x-fragments.text-field label="Password" name="password" type="password"
                placeholder="Kosongkan jika tidak ingin mengubah password" />

            <div class="mb-4">
                <label for="role-{{ $user->id }}" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role-{{ $user->id }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="jamaah" {{ $user->role == 'jamaah' ? 'selected' : '' }}>Jamaah</option>
                </select>
            </div>
        </x-fragments.form-modal>
    @endforeach
@endsection
