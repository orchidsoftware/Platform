<div class="dropdown d-inline-block" data-controller="screen--filter">
    <button class="btn btn-sm btn-link dropdown-toggle"
            type="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            data-boundary="viewport"
            aria-expanded="false">
        <x-orchid-icon path="filter"/>
    </button>
    <div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow" x-placement="bottom-end">
        <div class="wrapper-sm" data-action="click->screen--filter#onMenuClick">
            <div data-controller="fields--datetime" class="input-group" 
                data-fields--datetime-inline="true"
                data-fields--datetime-mode="range"
              >
              <input class="d-none" 
                     name="filter[{{$column}}]"
                     data-target="fields--datetime.instance"
                     value="{{get_filter_string($column)}}"
                     form="filters"
                     placeholder="{{ __('Filter') }}"
              >
            </div>

            <div class="line line-dashed border-bottom my-3"></div>
            <button type="submit" form="filters" class="btn btn-default btn-sm w-100">{{__('Apply')}}</button>
        </div>
    </div>
</div>