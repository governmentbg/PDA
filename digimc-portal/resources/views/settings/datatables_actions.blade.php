{{ html()->form('delete', route('manage.settings.destroy', $id))->open() }}
<div class='btn-group'>
    <a  class="btn btn-primary btn-sm" href="{{route('manage.settings.edit', $id)}}"><i class="fa fa-edit"></i></a>

    {!! html()->button('<i class="fa fa-trash"></i>')->attributes([
        'type' => 'submit',
        'class' => 'btn btn-danger btn-xs',
        'onclick' => "return confirm('Are you sure?')"
    ]) !!}
</div>
{!! html()->form()->close() !!}
