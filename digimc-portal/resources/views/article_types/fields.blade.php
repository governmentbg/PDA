<!-- Keyword Field -->
<div class="form-group col-sm-6">
    {!! html()->label('Име:', 'name') !!}
    {!! html()->text('name', null)->attributes(['class' => 'form-control']) !!}
</div>


<!-- Submit Field -->
<div class="form-group col-sm-12 pt-4 d-flex justify-content-between">
    <a href="{!! route('manage.article_type.index') !!}" class="btn btn-default">Откажи</a>
    {!! html()->submit('Запази')->attributes(['class' => 'btn btn-primary']) !!}
</div>
