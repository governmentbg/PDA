@if(!empty($object->audios) && count($object->audios) > 0)
    @php
        $audioFields = [
            'performer', 'producer', 'duration', 'recording_team',
            'audio_original_title', 'composer', 'author_of_arrangement',
            'text_author', 'editing_producer_name', 'date_recorded', 'broadcasting_date', 'sub_type'
        ];
    @endphp

    @foreach($object->audios as $audio)
        @foreach($audioFields as $field)
            @if(!empty($audio->$field))
                <tr>
                    <td>{{ __("cultural_object.web_resource.audio.$field") }}</td>
                    <td>
                        @if(is_iterable($audio->$field))
                            {{ implode(', ', is_array($audio->$field) ? $audio->$field : $audio->$field->toArray()) }}
                        @else
                            {{ $audio->$field }}
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
@endif
