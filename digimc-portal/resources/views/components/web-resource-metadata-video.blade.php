@if(!empty($object->videos) && count($object->videos) > 0)
    @php
        $videoFields = [
            'film_duration', 'actor', 'filmmaker', 'scenario_writer',
            'cameraman', 'producer', 'composer', 'mount',
            'production_director', 'editor', 'other_related_persons', 'premiere', 'sub_type'
        ];
    @endphp

    @foreach($object->videos as $video)
        @foreach($videoFields as $field)
            @if(!empty($video->$field))
                <tr>
                    <td>{{ __("cultural_object.web_resource.video.$field") }}</td>
                    <td>
                        @if(is_iterable($video->$field))
                            {{ implode(', ', is_array($video->$field) ? $video->$field : $video->$field->toArray()) }}
                        @else
                            {{ $video->$field }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
@endif
