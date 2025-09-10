@props(['id', 'title', 'action', 'method' => 'POST', 'size' => 'lg', 'show' => false, 'isDraft' => false])

<x-modal-layout :id="$id" :title="$title" :size="$size" :show="$show">
    <form action="{{ $action }}" method="POST" class="space-y-4 p-2" enctype="multipart/form-data">
        @csrf
        @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif

        {{ $slot }}

        <div class="flex justify-end space-x-1.5">
            @if ($isDraft)
                <button type="submit" name="_draft" value="1"
                    class="px-4 py-2 text-sm font-medium text-blue-500 rounded-lg border border-blue-500 hover:bg-gray-200 transition-colors duration-200">
                    Draft
                </button>
            @endif
            <button type="submit" name="_draft" value="0"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 transition-colors duration-200">
                <i class="fa-solid fa-save mr-2"></i>
                Simpan
            </button>
        </div>
    </form>
</x-modal-layout>
