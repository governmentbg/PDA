@props(['item', 'resource'])

<div class="container">
    <div id="cultural-object-display"
         class="d-flex justify-content-center align-items-center"
         style="width: 100%; height: 600px; background: #000; position: relative;">

        @if($resource?->web_resource_address)
            <iframe src="{{ $resource?->web_resource_address }}"
                    style="width:100%; height:100%;" frameborder="0"></iframe>
        @else
            <div class="text-center text-white">
                <p>{{ __('errors.the_object_cannot_be_visualized') }}</p>
            </div>
        @endif
    </div>


</div>

