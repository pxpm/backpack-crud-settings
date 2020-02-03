{{-- Show the inputs --}}
@php
$groupedFields = [];

if(!$fields->isEmpty()) {
    $groupedFields = $fields->groupBy('group');
   
}
    //$fields = $fields->groupBy('group');
    //dd($fields);
@endphp

@foreach ($groupedFields as $fieldGroup => $fields)
    <!-- load the view from type and view_namespace attribute if set -->
    <div class="card card-accent-dark mb-3 col-lg-12" style="-webkit-box-shadow: none; -moz-box-shadow: none; box-shadow: none; border-left: 0px solid; border-right: 0px solid; border-top: 0px solid; border-bottom: 0px solid;">
        <div class="card-header">{{$fieldGroup}}</div>
        <div class="card-body text-dark">
         
          @foreach($fields as $field)
    @php
        $fieldsViewNamespace = $field['view_namespace'] ?? 'crud::fields';
    @endphp

    @include($fieldsViewNamespace.'.'.$field['type'], ['field' => $field])
    @endforeach
        </div>
      </div>
    
@endforeach