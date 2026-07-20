@if(!empty($object->text_objects) && count($object->text_objects) > 0)
    @php
        $textFields = [
            'first_author', 'writer', 'translator',
            'date_of_publication', 'sponsor', 'sub_type'
        ];
    @endphp

    @foreach($object->text_objects as $text)
        @foreach($textFields as $field)
            @if(!empty($text->$field))
                <tr>
                    <td>{{ __("cultural_object.web_resource.text.$field") }}</td>
                    <td>
                        @if(is_iterable($text->$field))
                            {{ implode(', ', is_array($text->$field) ? $text->$field : $text->$field->toArray()) }}
                        @else
                            {{ $text->$field }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
@endif
