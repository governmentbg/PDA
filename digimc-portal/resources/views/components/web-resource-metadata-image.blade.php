@if(!empty($object->images))
    @foreach($object->images as $image)
        @if(!empty($image->sub_type))
            <tr>
                <td>{{ __('cultural_object.web_resource.sub_type') }}</td>
                <td>{{ $image->sub_type }}</td>
            </tr>
        @endif
    @endforeach
@endif
