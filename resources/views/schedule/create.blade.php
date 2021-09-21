@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')

<link rel="stylesheet" href="https://fullcalendar.io/js/fullcalendar-3.1.0/fullcalendar.css">
<link rel="stylesheet" href="https://fullcalendar.io/js/fullcalendar-scheduler-1.5.0/scheduler.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection


@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">Create</li>
  </ol>
@endsection

@section('page-action')
  <a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">List<i class="material-icons right">list</i></a>
@endsection

<div class="section">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsum is used for the ...</p>
    </div>
  </div>
  
  <!--Basic Form-->
  <div class="row">
    <!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
            <h4 class="card-title">{{ $page->title ?? ''}} Form</h4>

            <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text"><ul></ul></div></div>
            <div class="row">
                <div class="col s12">
                  <div id="calendar"></div>
                </div>
              </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="modal1" class="modal">
  <div class="modal-content">
    <form id="pop_form">
      <div class="row">
        <div class="pf_hd col m6 s12 l6">
          <h2>Please fill the form below</h2>
        </div>
        <!-- <div class="pf_con">
          <div class="pf_bar">
            <span class="confirm_bkg">Confirm booking</span>
            <span class="check_bkg">Check in </span>
            <span class="recieve_bkg">Received payment</span>
          </div>
        </div> -->
      </div>
      <div class="row">
        <div class="input-field col m4 s4 l4">
          <input id="first_name01" type="text">
          <label for="first_name01" class="" id="user_name">Name</label>
        </div>
        <div class="input-field col m4 s4 l4">
          <input id="last_name" type="text">
          <label for="last_name">Phone Number</label>
        </div>
        <div class="input-field col m4 s4 l4">
          <input id="email5" type="email">
          <label for="email">Email</label>
        </div>
      </div>            
      <div class="row">
        <div class="input-field col m6 s6">
          <div class="select-wrapper">
              <select tabindex="-1">
                  <option value="" disabled="" selected="">Service/Package</option>
                  <option value="1">Manager</option>
                  <option value="2">Developer</option>
                  <option value="3">Business</option>
              </select>
          </div>
          <label>Select Profile</label>
        </div>  
        <div class="input-field col m6 s6">
          <div class="select-wrapper">
              <select tabindex="-1">
                  <option value="" disabled="" selected="">Duration</option>
                  <option value="1">10 min</option>
                  <option value="1">20 min</option>
                  <option value="1">30 min</option>
                  <option value="2">60 min</option>
                  <option value="3">90 min</option>
              </select>
          </div>
          <label>Select Profile</label>
        </div>              
      </div>        
    </form> 
  </div>
  <div class="modal-footer">
    <div class="row buttonRow">
      <button class="btn recv_payment" type="submit">Receive payment
      </button>   
      <button class="btn" type="submit">Submit
          <i class="material-icons right">send</i>
      </button>   
    </div>
  </div>
</div>

@include('schedule.manage')

@endsection

{{-- vendor scripts --}}
@section('vendor-script')

@endsection


@push('page-scripts')
<script src="{{ asset('admin/js/common-script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.js"></script>
<script src="https://fullcalendar.io/js/fullcalendar-3.1.0/fullcalendar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- Fullcalendar -->
<script src="{{asset('admin/js/custom/fullcalendar.js')}}"></script>
<script src="{{asset('admin/js/custom/daypilot-all.min.js')}}"></script>
<script>

var timePicker  = {!! json_encode($variants->time_picker) !!};
var timezone    =   {!! json_encode($variants->timezone) !!};
var timeFormat  = {!! json_encode($variants->time_format) !!};
var therapists  = '';
var today       = '';

$(function() {
 

  $('input[name="start"]').daterangepicker({
      singleDatePicker: true,
      startDate: new Date(),
      showDropdowns: true,
      autoApply: true,
      timePicker: true,
      timePicker24Hour: timePicker,
      locale: { format: 'DD-MM-YYYY '+timeFormat+':mm A' },
  }, function(ev, picker) {
    // console.log(picker.format('DD-MM-YYYY'));
  })

});

// Form script start
$('#service_type').select2({ placeholder: "Please select type"});
$('#services').select2({ placeholder: "Please select service", allowClear: true });
$('#packages').select2({ placeholder: "Please select package" });
$('#user_id').select2({ placeholder: "Please select therapist" });

$(document).on('change', '#service_type', function () {
  if( this.value == 1 ){
    $("#services_block").show();
    $("#packages_block").hide();
    getServices();
  }else{
    $("#services_block").hide();
    $("#packages_block").show();
    getPackages();
  }
});

function getServices(){
  $.ajax({
      type: 'GET',
      url: "{{ url(ROUTE_PREFIX.'/common/get-all-services') }}",
      dataType: 'json',
      delay: 250,
      success: function(data) {
          var selectTerms = '<option value="">Please select service</option>';
          $.each(data.data, function(key, value) {
            selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
          });

          var select = $('#services');
          select.empty().append(selectTerms);
      }
  });
}

function getPackages(){
  $.ajax({
      type: 'GET',
      url: "{{ url(ROUTE_PREFIX.'/common/get-all-packages') }}",
      dataType: 'json',
      delay: 250,
      success: function(data) {
          var selectTerms = '<option value="">Please choose packages</option>';
          $.each(data.data, function(key, value) {
            selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
          });

          var select = $('#packages');
          select.empty().append(selectTerms);
      }
  });
}

var path        = "{{ route('billing.autocomplete') }}";
$('input.typeahead').typeahead({
    autoSelect: true,
    hint: true,
    highlight: true,
    minLength: 3,
    source:  function (query, process) {
    return $.get(path, 
        { 
          search: query,
          classNames: {
                        input: 'Typeahead-input',
                        hint: 'Typeahead-hint',
                        selectable: 'Typeahead-selectable'
                    }
        }, function (data) {
            return process(data);
        });
    },
    updater: function (item) {
      $('#customer_id').val(item.id);
      getCustomerDetails(item.id);
      return item;
    }
});

function getCustomerDetails(customer_id){
  $.ajax({
      type: 'POST',
      url: "{{ url(ROUTE_PREFIX.'/common/get-customer-details') }}",
      dataType: 'json', data: { customer_id:customer_id},
      delay: 250,
      success: function(data) {
        $(".label-placeholder").addClass("active");
        // $("#search_customer").val(data.data.name + ' - ' + data.data.mobile);
        $("#customer_id").val(customer_id);
        $("#customer_name").val(data.data.name);
        $("#mobile").val(data.data.mobile);
        $("#email").val(data.data.email);
        $("#customer_id").val(customer_id);
      }
  });
}

function clearForm(){
  // validator.resetForm();
  // $('input').removeClass('error');
  $("#manageScheduleForm .form-control").removeClass("error");
  $('select').removeClass('error');
  $('#manageScheduleForm').trigger("reset");
  $('#manageScheduleForm').find("input[type=text], textarea, hidden").val("");
  $('#service_type').select2({ placeholder: "Please select type"});
  $('#services').select2({ placeholder: "Please select service", allowClear: true });
  $('#packages').select2({ placeholder: "Please select package" });
}

$('.service-type').select2({ placeholder: "Please select ", allowClear: false }).on('select2:select select2:unselect', function (e) { 
  var type = $(this).data("type");
  listItemDetails(type) 
  $(this).valid()
});

function listItemDetails(type){
  var data_ids = $('#'+type).val();
  if(data_ids != ''){
    $.ajax({
        type: 'post',
        url: "{{ url(ROUTE_PREFIX.'/common/list-service-with-tax') }}",
        dataType: 'json',data: { data_ids:data_ids, type : type},delay: 250,
        success: function(data) {
            $('#grand_total').val(data.grand_total);

        }
    });
  }
}
// Form script ends



$( document ).ready(function(){

    getTherapists();

});

function getTherapists(){
  $.ajax({
    type: 'POST', url: "{{ url(ROUTE_PREFIX.'/common/get-therapists') }}", delay: 250,
    success: function(data) {
      if(data.flagError == false){
        therapists = data.data;
        loadCalendar();
      }

    }
  });
}


function loadCalendar(){
  var calendar = $('#calendar').fullCalendar({
      timeZone: timezone,
      defaultView: 'agendaDay',
      slotDuration: '00:05:00', 
      displayEventTime: false,
      editable: true,
      // timeFormat: timeFormat+':mm A',
      selectable: true,
      minTime: '08:55:00',
      maxTime: '22:05:00',
      eventLimit: true, // allow "more" link when too many events
      header: {
        left: 'prev,next today',
        right: 'month,agendaWeek,agendaDay'
      },
      allDaySlot: false,
      resources: therapists,
      events: "{{ url(ROUTE_PREFIX.'/schedules') }}",
      eventRender: function (event, element, view) {
          if (event.allDay === 'true') {
              event.allDay = true;
          } else {
              event.allDay = false;
          }
          element.find('.fc-title').append("<br/><br/>" + event.description); 
      },
      select: function(start, end, jsEvent, view, resource) {

          $("#user_id").val(resource.id);
          $("#start").val(start.format(timeFormat+':mm A'));
          $("#start_time").val(start.format());
          $('#user_id').select2().trigger('change');
          // alert(start.format(timeFormat+':mm A'));
          $('#manage-schedule-modal').modal('open');
          

        

          // $('#sel_users').val(value);
          // $('#sel_users').select2().trigger('change');


          var forms   = $("#manageScheduleForm");

            if ($("#manageScheduleForm").length > 0) {
                var validator = $("#manageScheduleForm").validate({ 
                    rules: {
                      customer_name: {
                              required: true,
                              maxlength: 200,
                              lettersonly: true,
                      },
                      mobile:{
                            minlength:10,
                            maxlength:10
                      },
                      email: {
                        email: true,
                      },
                      "bill_item[]": {
                              required: true,
                      },
                    },
                    messages: { 
                        customer_name: {
                            required: "Please enter customer name",
                            maxlength: "Length cannot be more than 200 characters",
                            },
                        mobile: {
                            maxlength: "Length cannot be more than 10 numbers",
                            minlength: "Length must be 10 numbers",
                            },
                        email: {
                            email: "Please enter a valid email address.",
                        },
                        "bill_item[]": {
                            required: "Please select an item",
                        },
                    },
                    submitHandler: function (form) {
                          $.ajax({
                              url: "{{ url(ROUTE_PREFIX.'/schedules/save-booking') }}", data: forms.serialize(), type: "POST",
                              success: function (data) {

                                $('#manage-schedule-modal').modal('close');
                                showSuccessToaster("Created successfully");
                                  calendar.fullCalendar('renderEvent', {
                                      id: data.id,
                                      resourceId: data.user_id,
                                      start: data.start,
                                      end: data.end,
                                      title: data.name,
                                      description: data.description ,
                                      // allDay: allDay
                                  }, true);
                                  calendar.fullCalendar('unselect');
                                  clearForm();
                              }
                          });
                          clearForm();
                    },
                    errorPlacement: function(error, element) {
                      if (element.is("select")) {
                          error.insertAfter(element.next('.select2'));
                      }else {
                          error.insertAfter(element);
                      }
                    },
                })
            }
        // console.log( 'select',   start.format(),    end.format(),    resource ? resource.id : '(no resource)'   );
      },
      eventClick: function(event) {
          var event_id = event.id ;
          console.log(event)
          $.ajax({
            type: 'GET', url: "{{ url(ROUTE_PREFIX.'/schedules') }}/"+event_id, delay: 250,
            success: function(data) {
              if(data.flagError == false){
                $("#customer_name").val(data.data.name);
                $("#mobile").val(data.data.mobile);


              }

            }
          });

      },
      dayClick: function(date, jsEvent, view) {
          //$('#modal1').modal('open');
      }

  });
}


function getSchedule(id){
  $.ajax({
    type: 'POST', url: "{{ url(ROUTE_PREFIX.'/schedules/') }}"+id, delay: 250,
    success: function(data) {
      // if(data.flagError == false){
        return data;

      // }

    }
  });
}

jQuery.validator.addMethod("lettersonly", function (value, element) {
  return this.optional(element) || /^[a-zA-Z()._\-\s]+$/i.test(value);
}, "Letters only please");
 

</script>
@endpush

