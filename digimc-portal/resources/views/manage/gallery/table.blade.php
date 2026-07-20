@push('styles')
    @include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%','id' => 'gallery-table']) !!}

@push('scripts')
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit-btn', function() {
                const name = $(this).data('name');
                const description = $(this).data('description');
                const actionUrl = $(this).data('action-route');

                const modal = $('#editGalleryModal');
                const form = modal.find('#editGalleryForm');

                form.attr('action', actionUrl);

                form.find('input[name="name"]').val(name);
                form.find('textarea[name="description"]').val(description);

                modal.modal('show');
            });

            $('#editGalleryForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const actionUrl = form.attr('action');
                const formData = form.serialize();

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#editGalleryModal').modal('hide');

                        if (window.LaravelDataTables && LaravelDataTables["gallery-table"]) {
                            LaravelDataTables["gallery-table"].ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Грешка при обновяване: ' + (xhr.responseJSON?.message || 'Нещо се обърка.'));
                    }
                });
            });
            $(document).on('click', '.action-btn', function() {
                let button = $(this);
                let url = button.data('action-route');
                let headerClass = button.data('header-class');
                let submitClass = headerClass.replace('bg-', 'btn-');

                $('#actionForm').attr('action', url);

                $('#modalHeader').removeClass('bg-danger bg-warning').addClass(headerClass);
                $('#modalTitle').html('<i class="fa fa-exclamation-triangle me-2"></i>' + button.data('title'));

                $('#modalSubmitBtn').removeClass('btn-danger btn-warning').addClass(submitClass).html(button.data('submit-text'));

                $('#actionModal').modal('show');
            });
        });
    </script>
@endpush
