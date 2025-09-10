@props([
    'url' => '#',
    'title' => 'Publish',
    'message' => 'Yakin ingin mengpublish data ini?',
])

<form method="POST" action="{{ $url }}" class="inline">
    @csrf
    <button data-tooltip-target="tooltip-default" type="button" onclick="confirmArchive(this)"
        data-title="{{ $title }}" data-message="{{ $message }}"
        class="inline-flex items-center p-3 text-xs font-medium cursor-pointer text-cyan-600 bg-cyan-100 rounded-md hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-1 transition-colors duration-200">
        <i class="fa-solid fa-arrow-up-from-bracket"></i>
    </button>


    <div id="tooltip-default" role="tooltip"
        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip">
        publish Data
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>
</form>
<script>
    function confirmArchive(button) {
        const form = button.closest('form');
        const title = button.getAttribute('data-title') || 'Publish';
        const message = button.getAttribute('data-message') || 'Yakin ingin mempublish data ini?';

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
