@component($typeForm, get_defined_vars())
    <div  data-controller="fields--ckeditor" data-fields--ckeditor-toolbar="{{$toolbar}}">
    <div id="toolbar"></div>
      <div class="ckeditor p-3 position-relative" id="ckeditor-wrapper-{{$id}}"
              style="min-height: {{ $attributes['height'] }}">
      </div>
      <input class="d-none" @attributes($attributes)>
    </div>
@endcomponent
