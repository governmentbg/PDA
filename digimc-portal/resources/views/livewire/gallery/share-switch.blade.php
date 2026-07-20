<div>
    <input
        type="checkbox"
        data-toggle="switchbutton"
        data-onlabel="{{ __('gallery.public') }} "
        data-offlabel="{{ __('gallery.private') }} "
        data-onstyle="primary"
        data-offstyle="secondary"
        @checked($enabled)
        wire:change="toggle($event.target.checked)"
    >
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css" rel="stylesheet">
    <style>
        .switch.btn {
            min-width: 100px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>
    <script>
        function initSwitchButtons() {
            $('[data-toggle="switchbutton"]').each(function () {
                if (!$(this).data('switch-initialized')) {
                    $(this).bootstrapSwitch();
                    $(this).data('switch-initialized', true);
                }
            });
        }

        document.addEventListener('livewire:load', () => {
            initSwitchButtons();
            Livewire.hook('message.processed', () => initSwitchButtons());
        });
    </script>
@endpush


