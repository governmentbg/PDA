
<div class='btn-group'>

    @if($row->status != \App\Enums\ArticleEnum::STATUS_DRAFT && !is_null($row->published_at))
        {{ html()->form(action: route('manage.article.toggle-publish', $id), method: 'post')->open() }}
        {!! html()->button('<i class="fa fa-warning"  title="Разпубликувай"></i>')->attributes([
    'type' => 'submit',
    'class' => 'btn btn-warning btn-sm',
    'onclick' => "return confirm('Сигурни ли сте, че искате да разпубликувате тази новина?')"
]) !!}
        {!! html()->form()->close() !!}
        @else

        {{ html()->form('post', route('manage.article.toggle-publish', $id))->open() }}
        {!! html()->button('<i class="fa fa-play" title="Публикувай"></i>')->attributes([
    'type' => 'submit',
    'class' => 'btn btn-success btn-sm',
    'onclick' => "return confirm('Сигурни ли сте, че искате да публикувате тази новина?')"
]) !!}
        {!! html()->form()->close() !!}
        @endif

        @if(!empty($row->image))
            <a class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#imageModal{{$row->id}}"><i class="fa fa-image"></i></a>

            <!-- Modal -->
            <div class="modal fade" id="imageModal{{$row->id}}" tabindex="-1" aria-labelledby="imageModal{{$row->id}}Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="imageModal{{$row->id}}Label">Снимка</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img class="img-fluid" src="{{asset($row->image->filepath)}}" alt="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Затвори</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    <a  class="btn btn-primary btn-sm" href="{{route('manage.article.edit', $id)}}"><i class="fa fa-edit"></i></a>

    {{ html()->form('delete', route('manage.article.destroy', $id))->open() }}
    {!! html()->button('<i class="fa fa-trash"  title="Изтрий"></i>')->attributes([
        'type' => 'submit',
        'class' => 'btn btn-danger btn-sm',
        'onclick' => "return confirm('Сигурни ли сте че искате да изтриете тази новина?')"
    ]) !!}
    {!! html()->form()->close() !!}
</div>

