<form wire:submit.prevent="search" class="search-form-container">
    <div x-data="{ open:false }"
         class="position-relative"
         @click.away="open=false"
         @keydown.escape.window="open=false">

        <div style="display:flex;align-items:center;border:1px solid #ccc;border-radius:5px;overflow:hidden;">
            <input type="text"
                   wire:model="query"
                   wire:keydown.enter.prevent="search"
                   placeholder="{{ __('navbar.search') }}"
                   class="search-input d-none d-sm-block"
                   style="flex-grow:1;padding:6px 10px;border:none;outline:none;box-shadow:none;font-size:14px;height:36px;width:430px;"
                   @focus="open=true"/>

            <button type="submit"
                    aria-label="Търсене"
                    class="search-button"
                    style="background:#337ab7;color:white;padding:6px 10px;border:none;cursor:pointer;height:36px;display:flex;align-items:center;justify-content:center;min-width:36px;">
                <i class="bi bi-search"></i>
            </button>
        </div>

        <div x-cloak
             x-show="open"
             @mousedown.prevent
             class="dropdown-menu show mt-1 shadow"
             style="display:block; position:absolute; left:0; top:100%; width:100%; z-index:1050;">
            <div class="px-3 py-2">
                <button type="button"
                        wire:click="openAdvancedSearch"
                        class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2 advanced-search-dropdown-btn">
                        {{ __('search.advanced_search') }}
                </button>
            </div>
        </div>
    </div>
</form>
