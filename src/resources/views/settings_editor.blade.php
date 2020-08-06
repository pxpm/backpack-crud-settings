@extends(backpack_view('layouts.top_left'))

@section('content')

<div class="row">
	<div class="col-lg-12">
		<!-- Default box -->
@if(count($crud->fields()) > 0)
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
		  <button type="button" class="btn btn-primary" id="saveButton">{{trans('bpsettings::bpsettings.save_button_text')}}</button>
		  @else
		<h3>{{ trans('bpsettings::bpsettings.no_settings_in_database')}}</h3>
		  @endif
	</div>
</div>

@endsection