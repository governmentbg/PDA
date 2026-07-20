<!-- Article Type Id Field -->
<div class="form-group col-12">
    {!! html()->label('Тип:', 'article_type_id') !!}
    {!! html()->select('article_type_id', $article_types->pluck('name','id'), null)->attributes(['class' => 'form-control select2', 'placeholder' => 'Изберете', 'required']) !!}
</div>

<!-- Title Field -->
<div class="form-group col-12">
    {!! html()->label('Заглавие:', 'title') !!}
    {!! html()->text('title', null)->attributes(['class' => 'form-control', 'required']) !!}
</div>

<!-- Slug Field -->
<div class="form-group col-12">
    {!! html()->label('Адрес генериран за досъп през сайта:','slug') !!}
    {!! html()->text('slug', null)->attributes(['class' => 'form-control', 'readonly']) !!}
</div>

<!-- Content Field -->
<div class="form-group col-12">
    {!! html()->label('Новина:', 'content') !!}
    {!! html()->textarea('content', null)->attributes(['class' => 'form-control summernote', 'required']) !!}
</div>

@if(isset($article) && !empty($article->image))
    <div class="row">
        <div class="col-4">
            <img class="img-fluid p-3" src="{{asset($article->image->filepath)}}" alt="{{$article->image->filename}}">
        </div>
        <div class="col-1 d-flex justify-content-center align-items-center">
            <a onclick="return confirm('Сигурни ли сте че искате да изтриете тази снимка от новината?')" href="{{route('manage.article.deleteImage', ['articleId' => $article->id, 'imageId' => $article->image->id])}}" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
@endif

<!-- image Field -->
<div class="form-group col-12">
    {!! html()->label('Снимка:', 'image') !!}
    {!! html()->file('image')->attributes(['class' => 'form-control']) !!}
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12 pt-4 d-flex justify-content-between">
    <a href="{!! route('manage.article.index') !!}"
       class="btn btn-default"
       onclick="return confirm('Сигурни ли сте, че искате да се откажете? Всички въведени данни ще бъдат загубени.');">
        Откажи
    </a>
    {!! html()->submit('Запази')->attributes(['class' => 'btn btn-primary']) !!}
</div>


@push('scripts')
    <script>
        $(document).ready(function(){
            $('.summernote').summernote({
                height: 400,
                placeholder: 'Въведете съдържание тук...',
                tabsize: 2
            });

            $('#title').on('focusout', function() {
                let title = $(this).val();

                if (title.length > 0) {
                    $.get("{{ route('manage.article.slugify') }}", { title: title }, function(data) {
                $('#slug').val(data.slug);
            });
        } else {
            $('#slug').val('');
        }
    });
});
    </script>
@endpush
