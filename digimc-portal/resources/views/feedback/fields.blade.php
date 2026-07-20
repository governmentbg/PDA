@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

{{-- Subject --}}
<div class="form-group col-sm-6 mb-3">
    {!! html()->label(__('feedback.modal.labels.subject'), 'subject')->attributes(['class' => 'form-label']) !!}
    {!! html()->text('subject', old('subject'))
        ->attributes([
            'id' => 'subject',
            'maxlength' => $limits['subject'],
            'required' => true,
            'class' => 'form-control' . ($errors->has('subject') ? ' is-invalid' : ''),
        ]) !!}
    <div class="invalid-feedback" data-error-for="subject">
        @error('subject') {{ $message }} @enderror
    </div>
</div>

{{-- Category --}}
<div class="form-group col-sm-6 mb-3">
    <label for="category" class="form-label">{{ __('feedback.modal.labels.category') }}</label>
    <select name="category" id="category" required class="form-select {{ $errors->has('category') ? 'is-invalid' : '' }}">
        <option value=""></option>

        @foreach ($categories as $category)
            <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                {{ __('feedback.categories.' . Str::ucfirst($category)) }}
            </option>
        @endforeach
    </select>
    <div class="invalid-feedback" data-error-for="category">
        @error('category') {{ $message }} @enderror
    </div>
</div>

{{-- Description --}}
<div class="form-group col-sm-12 mb-3">
    {!! html()->label(__('feedback.modal.labels.description'), 'description')->attributes(['class' => 'form-label']) !!}
    {!! html()->textarea('description', old('description'))
        ->attributes([
            'id' => 'description',
            'rows' => 5,
            'maxlength' => $limits['description'],
            'required' => true,
            'class' => 'form-control' . ($errors->has('description') ? ' is-invalid' : ''),
        ]) !!}
    <div class="invalid-feedback" data-error-for="description">
        @error('description') {{ $message }} @enderror
    </div>
</div>

{{-- Email --}}
<div class="form-group col-sm-6 mb-3">
    {!! html()->label(__('feedback.modal.labels.email'), 'contact_email')->attributes(['class' => 'form-label']) !!}
    {!! html()->email('contact_email', old('contact_email'))
        ->attributes([
            'id' => 'contact_email',
            'maxlength' => $limits['email'],
            'required' => true,
            'class' => 'form-control' . ($errors->has('contact_email') ? ' is-invalid' : ''),
        ]) !!}
    <div class="invalid-feedback" data-error-for="contact_email">
        @error('contact_email') {{ $message }} @enderror
    </div>
</div>

{{-- Name --}}
<div class="form-group col-sm-6 mb-3">
    {!! html()->label(__('feedback.modal.labels.name'), 'name')->attributes(['class' => 'form-label']) !!}
    {!! html()->text('name', old('name'))
        ->attributes([
            'id' => 'name',
            'maxlength' => $limits['name'],
            'required' => true,
            'class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''),
        ]) !!}
    <div class="invalid-feedback" data-error-for="name">
        @error('name') {{ $message }} @enderror
    </div>
</div>
