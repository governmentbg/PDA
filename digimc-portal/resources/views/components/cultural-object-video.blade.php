@props(['item', 'resource'])

@push('styles')
    <link href="https://vjs.zencdn.net/8.23.3/video-js.css" rel="stylesheet"/>
@endpush
<div class="container">
    <div class="mx-auto" style="max-width: 640px;">
        <video
            id="my-video"
            class="video-js"
            controls
            preload="auto"
            width="640"
            height="350"
            poster="{{ $item->thumbnail_url }}"
            crossorigin="anonymous"
            data-setup="{}"
        >
            <source
                src="{{ $resource?->is_locked ? $resource?->trailer_address : $resource?->web_resource_address }}"
                type="{{ $resource?->resource_type }}"
            />
        </video>
    </div>


</div>

@push('scripts')
    <script src="https://vjs.zencdn.net/8.23.3/video.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/videojs-hls-quality-selector@2.0.0/dist/videojs-hls-quality-selector.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let player;
            let videoErrorMessage = "{{ __('errors.video_error') }}";
            if (videojs.getPlayer('my-video')) {
                player = videojs.getPlayer('my-video');
            } else {
                player = videojs('my-video', {
                    html5: {
                        vhs: {
                            overrideNative: true
                        },
                        nativeAudioTracks: false,
                        nativeVideoTracks: false
                    }
                });
            }

            player.ready(function () {
                const resourceAddress = "{{ $resource?->is_locked ? $resource?->trailer_address : $resource?->web_resource_address }}";
                const resourceType = "{{ $resource?->resource_type }}";

                loadSignedUrl(resourceAddress, function(fullUrl) {

                    const bustableResourceAddress = fullUrl + (fullUrl.includes('?') ? '&' : '?') + 't=' + Date.now();

                    player.src({
                        src: bustableResourceAddress,
                        type: resourceType
                    });


                    if (player.tech_?.vhs?.xhr) {
                        player.tech_.vhs.xhr.beforeRequest = function(options) {
                            options.uri += (options.uri.includes('?') ? '&' : '?') + 't=' + Date.now();
                            return options;
                        };
                    }


                    if (player.hlsQualitySelector) {
                        player.hlsQualitySelector({ displayCurrentQuality: true });
                    }
                });
            });

            player.on('error', function() {
                const error = player.error();
                if (error && error.code === 4) {
                    const container = document.querySelector('.video-js');
                    if (container) {
                        container.innerHTML = `
                            <div class="vjs-error-display vjs-modal-dialog" style="display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.85);">
                                <div class="vjs-modal-dialog-content" style="color: white; text-align: center; padding: 20px; font-family: sans-serif;">
                                    ${videoErrorMessage}
                                </div>
                            </div>`;
                    }
                }
            });


            function loadSignedUrl(fileKey, callback) {
                $.ajax({
                    url: "{{ route('cultural_object.video.sign') }}",
                    type: "POST",
                    contentType: "application/json",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    data: JSON.stringify({ file_key: fileKey }),
                    success: function(data) {
                        const fullUrl = data.url.startsWith('http') ? data.url : window.location.origin + data.url;
                        callback(fullUrl);
                    },
                    error: function(err) {
                        console.error("AJAX Error:", err);
                    }
                });
            }
        });
    </script>
@endpush
