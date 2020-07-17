<input type="hidden" name="http_referrer" value={{ old('http_referrer') ?? \URL::previous() ?? url($crud->route) }}>
{{-- See if we're using tabs --}}
@if ($crud->tabsEnabled() && count($crud->getTabs()))
    @include('bpsettings::backpack_overrides.show_tabbed_fields')
    <input type="hidden" name="current_tab" value="{{ Str::slug($crud->getTabs()[0], "") }}" />
@else
  <div class="card">
    <div class="card-body row">
      @include('bpsettings::backpack_overrides.show_fields', ['fields' => $crud->fields()])
    </div>
  </div>
@endif

{{-- Define blade stacks so css and js can be pushed from the fields to these sections. --}}

@section('after_styles')
    <!-- CRUD FORM CONTENT - crud_fields_styles stack -->
    @stack('crud_fields_styles')
@endsection

@section('after_scripts')
    <!-- CRUD FORM CONTENT - crud_fields_scripts stack -->
    @stack('crud_fields_scripts')

    <script>
    function initializeFieldsWithJavascript(container) {
      var selector;
      if (container instanceof jQuery) {
        selector = container;
      } else {
        selector = $(container);
      }
      selector.find("[data-init-function]").each(function () {
        var element = $(this);
        var functionName = element.data('init-function');

        if (typeof window[functionName] === "function") {
          window[functionName](element);
        }
      });
    }

    jQuery('document').ready(function($){

      // trigger the javascript for all fields that have their js defined in a separate method
      initializeFieldsWithJavascript('form');

      $submitButton = $('#saveButton');

      $submitButton.on('click', function(e) {
        $form = document.getElementById("settingsSaveForm");
        
        //this is needed otherwise fields like ckeditor don't post their value.
        $($form).trigger('form-pre-serialize');
       

        var $formData = new FormData($form);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> saving...';
        if ($submitButton.html() !== loadingText) {
          $submitButton.data('original-text', $(this).html());
          $submitButton.html(loadingText);
          $submitButton.prop('disabled', true);
        }
        $.ajax({
            url: "{{route('bp-settings-save')}}",
            data: $formData,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (result) {
              console.log(result)
                swal({
                    title: "Settings Saved",
                    text: "Settings saved into database with success.",
                    icon: "success",
                    timer: 3000,
                    buttons: false,
                });
                $submitButton.prop('disabled', false);
                $submitButton.html($submitButton.data('original-text'));
            },
            error: function (result) {
                // Show an alert with the result
                var $errors = result.responseJSON.errors;
                let message = '';
                for (var i in $errors) {
                    message += $errors[i] + ' \n';
                }
                swal({
                    title: "Error saving settings.",
                    text: message,
                    icon: "error",
                    timer: 4000,
                    buttons: false,
                });
                $submitButton.prop('disabled', false);
                $submitButton.html($submitButton.data('original-text'));
            }
        });
      });
     
      // Place the focus on the first element in the form
      @if( $crud->getAutoFocusOnFirstField() )
        @php
          $focusField = array_first($fields, function($field) {
              return isset($field['auto_focus']) && $field['auto_focus'] == true;
          });
        @endphp

        @if ($focusField)
        @php
        $focusFieldName = !is_iterable($focusField['value']) ? $focusField['name'] : ($focusField['name'] . '[]');
        @endphp
          window.focusField = $('[name="{{ $focusFieldName }}"]').eq(0),
        @else
          var focusField = $('form').find('input, textarea, select').not('[type="hidden"]').eq(0),
        @endif

        fieldOffset = focusField.offset().top,
        scrollTolerance = $(window).height() / 2;

        focusField.trigger('focus');

        if( fieldOffset > scrollTolerance ){
            $('html, body').animate({scrollTop: (fieldOffset - 30)});
        }
      @endif

      // Add inline errors to the DOM
      @if ($crud->inlineErrorsEnabled() && $errors->any())

        window.errors = {!! json_encode($errors->messages()) !!};
        // console.error(window.errors);

        $.each(errors, function(property, messages){

            var normalizedProperty = property.split('.').map(function(item, index){
                    return index === 0 ? item : '['+item+']';
                }).join('');

            var field = $('[name="' + normalizedProperty + '[]"]').length ?
                        $('[name="' + normalizedProperty + '[]"]') :
                        $('[name="' + normalizedProperty + '"]'),
                        container = field.parents('.form-group');

            container.addClass('text-danger');
            container.children('input, textarea').addClass('is-invalid');

            $.each(messages, function(key, msg){
                // highlight the input that errored
                var row = $('<div class="invalid-feedback">' + msg + '</div>');
                row.appendTo(container);

                // highlight its parent tab
                @if ($crud->tabsEnabled())
                var tab_id = $(container).parent().attr('id');
                $("#form_tabs [aria-controls="+tab_id+"]").addClass('text-red');
                @endif
            });
        });

      @endif

      $("a[data-toggle='tab']").click(function(){
          currentTabName = $(this).attr('tab_name');
          $("input[name='current_tab']").val(currentTabName);
      });

      if (window.location.hash) {
          $("input[name='current_tab']").val(window.location.hash.substr(1));
      }

      });
    </script>
@endsection
