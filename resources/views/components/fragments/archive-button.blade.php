@props([
    'url' => '#',
    'title' => 'Arsipkan',
    'message' => 'Yakin ingin mengarsipkan data ini?',
])

<form method="POST" action="{{ $url }}" class="inline">
    @csrf
    @method('PUT')
    <button data-tooltip-target="tooltip-default" type="button" onclick="confirmArchive(this)"
        data-title="{{ $title }}" data-message="{{ $message }}"
        class="inline-flex items-center p-3 text-xs font-medium cursor-pointer text-yellow-600 bg-yellow-100 rounded-md hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-1 transition-colors duration-200">
        <i class="fa-solid fa-box-archive"></i>
    </button>


    <div id="tooltip-default" role="tooltip"
        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip">
        Arsipkan Data
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>
</form>
<script>
    function confirmArchive(button) {
        const form = button.closest('form');
        const title = button.getAttribute('data-title') || 'Arsipkan';
        const message = button.getAttribute('data-message') || 'Yakin ingin mengarsipkan data ini?';

        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Submit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
