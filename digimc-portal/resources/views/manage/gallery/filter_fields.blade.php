<div class="row">
    <!-- Status Field -->
    <div class="form-group col-sm-6">
        {{ html()->label('Статус:', 'status') }}
        {{ html()->select('status', \App\Enums\GalleryEnum::getReadableStatus(), $filtered_status ?? null)->class('form-control')->placeholder('Избери статус') }}
    </div>


    <!-- Submit Field -->
    <div class="form-group col-sm-6">
        {{ html()->button('Филтрирай')->id('btn-filter')->class('hide btn btn-success float-right mt-4') }}
    </div>

</div>
