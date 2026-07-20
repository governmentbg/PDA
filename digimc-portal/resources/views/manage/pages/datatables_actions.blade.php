<div class='btn-group'>
    @if($row->status != \App\Enums\PageEnum::STATUS_DRAFT)
        {{ html()->form('post', route('manage.page.toggle-publish', $id))->open() }}
        {!! html()->button('<i class="fa fa-warning" title="Разпубликувай"></i>')->attributes([
            'type' => 'submit',
            'class' => 'btn btn-warning btn-sm',
            'onclick' => "return confirm('Сигурни ли сте, че искате да разпубликувате тази страница?')"
        ]) !!}
        {{ html()->form()->close() }}
    @else
        {{ html()->form('post', route('manage.page.toggle-publish', $id))->open() }}
        {!! html()->button('<i class="fa fa-play" title="Публикувай"></i>')->attributes([
            'type' => 'submit',
            'class' => 'btn btn-success btn-sm',
            'onclick' => "return confirm('Сигурни ли сте, че искате да публикувате тази страница?')"
        ]) !!}
        {{ html()->form()->close() }}
    @endif

    <a class="btn btn-primary btn-sm" href="{{ route('manage.page.edit', $id) }}"><i class="fa fa-edit"></i></a>

    {{ html()->form('delete', route('manage.page.destroy', $id))->open() }}
    {!! html()->button('<i class="fa fa-trash" title="Изтрий"></i>')->attributes([
        'type' => 'submit',
        'class' => 'btn btn-danger btn-sm',
        'onclick' => "return confirm('Сигурни ли сте, че искате да изтриете тази страница?')"
    ]) !!}
    {{ html()->form()->close() }}
</div>
