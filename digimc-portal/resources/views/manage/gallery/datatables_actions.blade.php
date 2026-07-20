<div class='btn-group'>
    @if($row->status == \App\Enums\GalleryEnum::STATUS_PENDING)
        {{ html()->form(action: route('manage.gallery.approve', $row->id), method: 'patch')->open() }}
        {!! html()->button('<i class="fa fa-check"  title="Публикувай"></i>')->attributes([
'type' => 'submit',
'class' => 'btn btn-default btn-sm btn-success',
'onclick' => "return confirm('Сигурни ли сте, че искате да публикувате тази колекция. Това действие ще я направи видима и достъпна на сайта?')"
]) !!}
        {!! html()->form()->close() !!}
    @endif

        @if ($row->status == \App\Enums\GalleryEnum::STATUS_PENDING)
            <button
                class="btn btn-default btn-sm btn-danger action-btn"
                data-id="{{ $row->id }}"
                data-action-route="{{ route('manage.gallery.reject', ['gallery' => $row->id]) }}"
                data-title="Причина за Отхвърляне"
                data-header-class="bg-danger"
                data-submit-text='<i class="fa fa-times-circle me-1"></i> Отхвърли'
                title="Отхвърли заявката"
            >
                <i class="fa fa-times-circle"></i>
            </button>
        @endif

        @if ($row->status == \App\Enums\GalleryEnum::STATUS_PUBLIC)
            <button
                class="btn btn-default btn-sm btn-warning action-btn"
                data-id="{{ $row->id }}"
                data-action-route="{{ route('manage.gallery.reject', ['gallery' => $row->id]) }}"
                data-title="Причина за Премахване"
                data-header-class="bg-warning"
                data-submit-text='<i class="fa fa-minus-circle me-1"></i> Премахни'
                title="Премахни от публични"
            >
                <i class="fa fa-minus-circle"></i>
            </button>
        @endif
        <button
            class="btn btn-sm btn-primary edit-btn"
            data-id="{{ $row->id }}"
            data-name="{{ $row->name }}"
            data-description="{{ $row->description }}"
            data-action-route="{{ route('manage.gallery.update', $row->id) }}"
        >
            <i class="fa fa-edit"></i>
        </button>

</div>

