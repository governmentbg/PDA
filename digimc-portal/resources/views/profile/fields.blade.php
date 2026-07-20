{{-- Email (read-only) --}}
<div class="mb-3">
    {!! html()->label(__('profile.fields.email'))->class('form-label') !!}
    {!! html()->email('email', $user->email)->class('form-control')->disabled() !!}
</div>

{{-- First name --}}
<div class="mb-3">
    {!! html()->label(__('profile.fields.first_name'), 'first_name')->class('form-label') !!}
    {!! html()->text('first_name', old('first_name', $user->first_name))
          ->class('form-control')->attribute('maxlength', 255)->required() !!}
</div>

{{-- Last name --}}
<div class="mb-3">
    {!! html()->label(__('profile.fields.last_name'), 'last_name')->class('form-label') !!}
    {!! html()->text('last_name', old('last_name', $user->last_name))
          ->class('form-control')->attribute('maxlength', 255)->required() !!}
</div>

{{-- Profile photo --}}
<div class="mb-3">
    {!! html()->label(__('profile.fields.profile_photo'))->class('form-label d-block') !!}
    <div class="d-flex align-items-center gap-3">
        <div>
            @php
                $photo = $user->profile_image_path ? asset( $user->profile_image_path) : null;
            @endphp

            <img id="profile_image_preview"
                 src="{{$photo ?? ''}}"
                 alt="Current profile photo"
                 class="rounded border {{ $photo ? '' : 'd-none' }}"
                 style="width:96px;height:96px;object-fit:cover;">

            <div id="no_photo_placeholder"
                 class="rounded d-flex align-items-center justify-content-center text-center border bg-light {{ $photo ? 'd-none' : '' }}"
                 style="width:96px;height:96px;">
                {{ __('profile.fields.no_photo') }}
            </div>
        </div>

        {{-- Custom file picker --}}
        <div class="flex-grow-1">
            {!! html()->file('profile_image_path')
              ->id('profile_image_path')
              ->class('visually-hidden')
              ->attribute('accept','image/*')
              ->name('profile_image_path') !!}

            <div class="input-group">
                {!! html()->label(__('profile.ui.browse'), 'profile_image_path')
                    ->class('btn btn-outline-secondary mb-0') !!}

                {!! html()->text('profile_image_path_name', __('profile.ui.no_file_selected'))
                    ->id('profile_image_path_name')
                    ->class('form-control')
                    ->attribute('readonly', true) !!}
            </div>

            <div class="form-text">{{ __('profile.hints.photo_rules') }}</div>
        </div>
    </div>
</div>

<hr class="my-4">

{{-- Toggles: notifications --}}
<div class="row">
    {{-- Email notification --}}
    <div class="col-md-4 mb-3">
        {!! html()->label(__('profile.fields.wants_notifications'))->class('form-label d-block') !!}
        <div class="form-check form-check-inline">
            {!! html()->radio('wants_notifications')->value('1')->id('wn_yes')
                ->checked((int)old('wants_notifications', (int)$user->wants_notifications) === 1)
                ->class('form-check-input') !!}
            {!! html()->label(__('profile.yes'), 'wn_yes')->class('form-check-label') !!}
        </div>
        <div class="form-check form-check-inline">
            {!! html()->radio('wants_notifications')->value('0')->id('wn_no')
                ->checked((int)old('wants_notifications', (int)$user->wants_notifications) === 0)
                ->class('form-check-input') !!}
            {!! html()->label(__('profile.no'), 'wn_no')->class('form-check-label') !!}
        </div>
    </div>

    {{-- News subscription --}}
    <div class="col-md-4 mb-3">
        {!! html()->label(__('profile.fields.subscribed_news'))->class('form-label d-block') !!}
        <div class="form-check form-check-inline">
            {!! html()->radio('subscribed_news')->value('1')->id('sn_yes')
                  ->checked((int)old('subscribed_news', (int)$user->subscribed_news) === 1)
                  ->class('form-check-input') !!}
            {!! html()->label(__('profile.yes'),'sn_yes')->class('form-check-label') !!}
        </div>
        <div class="form-check form-check-inline">
            {!! html()->radio('subscribed_news')->value('0')->id('sn_no')
                  ->checked((int)old('subscribed_news', (int)$user->subscribed_news) === 0)
                  ->class('form-check-input')!!}
            {!! html()->label(__('profile.no'),'sn_no')->class('form-check-label') !!}
        </div>
    </div>

    {{-- Weekly newsletter --}}
    <div class="col-md-4 mb-3">
        {!! html()->label(__('profile.fields.subscribed_weekly'))->class('form-label d-block') !!}
        <div class="form-check form-check-inline">
            {!! html()->radio('subscribed_weekly')->value('1')->id('sw_yes')
                  ->checked((int)old('subscribed_weekly', (int)$user->subscribed_weekly) === 1)
                  ->class('form-check-input')!!}
            {!! html()->label(__('profile.yes'),'sw_yes')->class('form-check-label') !!}
        </div>
        <div class="form-check form-check-inline">
            {!! html()->radio('subscribed_weekly')->value('0')->id('sw_no')
                  ->checked((int)old('subscribed_weekly', (int)$user->subscribed_weekly) === 0)
                  ->class('form-check-input') !!}
            {!! html()->label(__('profile.no'),'sw_no')->class('form-check-label') !!}
        </div>
    </div>
</div>

<hr class="my-4">

{{-- Confirmation modal to update fields--}}
{!! html()->password('current_password')
      ->class('d-none')
      ->attributes(['tabindex' => '-1', 'aria-hidden' => 'true', 'autocomplete' => 'off']) !!}
