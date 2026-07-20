@if(!empty($object->three_ds) && count($object->three_ds) > 0)
    @php
        $threeDFields = [
            'design_house',
            'sub_type'
        ];
    @endphp

    @foreach($object->three_ds as $threeD)
        @foreach($threeDFields as $field)
            @if(!empty($threeD->$field))
                <tr>
                    <td>{{ __("cultural_object.web_resource.three_d.$field") }}</td>
                    <td>
                        @if(is_iterable($threeD->$field))
                            {{ implode(', ', is_array($threeD->$field) ? $threeD->$field : $threeD->$field->toArray()) }}
                        @else
                            {{ $threeD->$field }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
@endif
