<!-- Title Field -->
<div class="form-group col-12">
    {!! html()->label('Заглавие:', 'title') !!}
    {!! html()->text('title', null)->attributes(['class' => 'form-control', 'required', 'id' => 'title']) !!}
</div>

<!-- SEF / Slug Field -->
<div class="form-group col-12">
    {!! html()->label('Адрес (SEF):', 'sef_title') !!}
    {!! html()->text('sef_title', null)->attributes([
        'class' => 'form-control',
        'id' => 'sef_title'
    ]) !!}
</div>

<!-- Content Field -->
<div class="form-group col-12">
    {!! html()->label('Съдържание:', 'content') !!}
    {!! html()->textarea('content', null)->attributes(['class' => 'form-control summernote', 'required']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-12">
    {!! html()->label('Статус:', 'status') !!}
    {!! html()->select('status', [
        \App\Enums\PageEnum::STATUS_DRAFT => 'Draft',
        \App\Enums\PageEnum::STATUS_PUBLISHED => 'Published'
    ], null)->attributes(['class' => 'form-control']) !!}
</div>

@push('scripts')
    <script>
        $(document).ready(function() {

            $('.summernote').summernote({
                height: 400,
                placeholder: 'Въведете съдържание тук...',
                tabsize: 2
            });

            $('#title').on('focusout', function() {
                let title = $(this).val();
                let currentSlug = $('#sef_title').val();

                if(title.length > 0 && currentSlug.trim() === '') {
                    $.get("{{ route('manage.page.slugify') }}", { title: title }, function(data){
                        $('#sef_title').val(data.slug);
                    });
                }
            });
        });
    </script>
@endpush
