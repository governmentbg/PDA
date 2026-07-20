<div class="container my-4">
    <div class="d-flex justify-content-end">
        <div class="col-5">
            <input
                type="text"
                wire:model.defer="query"
                wire:input.debounce.300ms="search"
                class="form-control"
                placeholder="{{__('general.search')}} {{__('general.provider')}}..."
            >


    @if(strlen($query) >= 3)
        <div class="dropdown-menu show mt-1 shadow">
            @if(count($results) == 0)
                <div class="dropdown-item text-muted">{{__('general.no_results_found')}}</div>
            @else
                <table class="table table-hover mb-0">
                    <tbody>
                    @foreach($results as $provider)
                        <tr>
                            <td>
                                <a href="{{ route('provider.view', ['id' => $provider->id]) }}"
                                   class="text-decoration-none text-dark d-block">
                                    <strong>{{ $provider->title }}</strong>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
</div>
    </div>
</div>
