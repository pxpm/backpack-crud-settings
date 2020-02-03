@extends(backpack_view('layouts.top_left'))

@php
  $breadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
  ];

@endphp

@section('content')

<div class="row">
	<div class="col-lg-12">
		<!-- Default box -->

		@include('crud::inc.grouped_errors')

		  <form method="post"
		  		id="settingsSaveForm"
		  		action="#"
				@if ($crud->hasUploadFields('create'))
				enctype="multipart/form-data"
				
				@endif
		  		>
        {!! csrf_field() !!}
        
		      	@include('bpsettings::backpack_overrides.form_content', [ 'fields' => $crud->fields()])
		
		  </form>
		  <button type="button" class="btn btn-primary" id="saveButton">{{trans('backpack::crud.save')}}</button>
	</div>
</div>

@endsection