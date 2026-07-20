<!-- Keyword Field -->
<div class="form-group col-sm-6">
    {!! html()->label('Ключ:', 'keyword') !!}
    {!! html()->text('keyword', null)->attributes(['class' => 'form-control']) !!}
</div>

<!-- Value Field -->
<div class="form-group col-sm-6">
    {!! html()->label('Стойност:', 'value') !!}
    {!! html()->text('value', null)->attributes(['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12 pt-4 d-flex justify-content-between">
    <a href="{!! route('manage.settings.index') !!}" class="btn btn-default">Откажи</a>

    {!! html()->submit('Запази')->attributes(['class' => 'btn btn-primary align-end']) !!}
</div>
