@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/select2/select2-materialize.css')}}">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/jquery.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/select.dataTables.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/vendors/toastr/toastr.min.css') }}">
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/data-tables.css')}}">
@endsection


@section('content')

@section('breadcrumb')
  <h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ Str::plural($page->title) ?? ''}}</span></h5>
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/store/billings') }}">{{ $page->title ?? ''}}</a></li>
    <li class="breadcrumb-item active">Update</li>
  </ol>
@endsection

<!-- users edit start -->
<div class="section users-edit section-data-tables">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">{{ Str::plural($page->title) ?? ''}}. Lorem ipsume is used for the ...</p>
    </div>
  </div>
  <div class="card">
    <div class="card-content">
      <ul class="tabs mb-2 row">
        <li class="tab">
          <a class="display-flex align-items-center active" id="account-tab" href="#account">
            <i class="material-icons mr-1">person_outline</i><span>Account</span>
          </a>
        </li>
        <li class="tab">
          <a class="display-flex align-items-center" id="information-tab" href="#additionalTaxes">
            <i class="material-icons mr-2">account_balance_wallet</i><span>Taxes</span>
          </a>
        </li>
        <li class="tab">
          <a class="display-flex align-items-center" id="paymentTypes-tab" href="#paymentTypes">
            <i class="material-icons mr-2">payment</i><span>Payment types </span>
          </a>
        </li>
      </ul>

      <div class="divider mb-3"></div>
      <div class="row">
        @if($store)
          <div class="col s12" id="account">
            <!-- users edit account form start -->
            <div class="card-alert card red lighten-5 print-error-msg" style="display:none"><div class="card-content red-text">I am sorry, this service is currently not supported in your selected country. In case you wish to use this service in any country other than India, please leave a message in the contact us page, and we shall respond to you at the earliest.</div></div>
            <h4 class="card-title">{{ $page->title ?? ''}} Form</h4>
            <form id="storeBillingForm" name="storeBillingForm" role="form" method="" action="" class="ajax-submit">
              {{ csrf_field() }}
              {!! Form::hidden('billing_id', $billing->id ?? '' , ['id' => 'billing_id'] ); !!}
              <div class="row">
                <div class="input-field col m6 s12">
                    {!! Form::text('company_name', $billing->company_name ?? '', array('id' => 'company_name')) !!} 
                    <label for="company_name" class="label-placeholder">Company Name <span class="red-text">*</span></label>
                </div>
                <div class="input-field col m6 s12">
                    {!! Form::textarea('address', $billing->address ?? '', ['id' => 'address', 'class' => 'materialize-textarea', 'placeholder'=>'Address','rows'=>3]) !!}
                    <label for="address" class="label-placeholder">Address</label>
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                    {!! Form::text('pincode', $billing->pincode ?? '', array('id' => 'pincode')) !!} 
                    <label for="pincode" class="label-placeholder">Pincode</label>
                </div>
                <div class="input-field col m6 s12">
                    {!! Form::select('billing_country_id', $variants->countries , $billing->country_id ?? '' , ['id' => 'billing_country_id' ,'class' => 'select2 browser-default', 'placeholder'=>'Please select country']) !!}
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                    {!! Form::text('gst', $billing->gst ?? '', array('id' => 'gst', 'style' => "text-transform:uppercase")) !!} 
                    <label for="gst" class="label-placeholder">GST No</label>
                </div>
                <div class="input-field col m6 s12">
                  @if(!empty($variants->states))
                    {!! Form::select('billing_state_id', $variants->states , $billing->state_id ?? '' , ['id' => 'billing_state_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select state']) !!}
                  @else
                    {!! Form::select('billing_state_id', [] , '' , ['id' => 'billing_state_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select state']) !!}
                  @endif
                </div>
              </div>
              <div class="row">
                <div class="input-field col m6 s12">
                 @if(!empty($variants->currencies))
                    {!! Form::select('currency', $variants->currencies , $billing->currency ?? '' , ['id' => 'currency' ,'class' => 'select2 browser-default','placeholder'=>'Please select currency ']) !!}
                  @else
                    {!! Form::select('currency', [] , $billing->currency ?? '' , ['id' => 'currency' ,'class' => 'select2 browser-default','placeholder'=>'Please select currency ']) !!}
                  @endif
                </div>
                <div class="input-field col m6 s12">
                    @if(!empty($variants->districts))
                        {!! Form::select('billing_district_id', $variants->districts , $billing->district_id ?? '' , ['id' => 'billing_district_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select district']) !!}
                    @else
                        {!! Form::select('billing_district_id', [] , '' , ['id' => 'billing_district_id' ,'class' => 'select2 browser-default','placeholder'=>'Please select district']) !!}
                    @endif
                </div>
              </div>
              <div class="row">
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light" type="reset" name="reset">Reset <i class="material-icons right">refresh</i></button>
                  <button class="btn cyan waves-effect waves-light" type="submit" name="action" id="submit-btn">Submit <i class="material-icons right">send</i></button>
                </div>
              </div>
            </form>
            <!-- users edit account form ends -->
          </div>
          
          <div class="col s12" id="additionalTaxes">
            <h4 class="card-title">GST Percentage</h4>
            <form id="storeGSTForm" name="storeGSTForm" role="form" method="" action="" class="ajax-submit">
              {{ csrf_field() }}
              {!! Form::hidden('gst_billing_id', $billing->id ?? '' , ['id' => 'gst_billing_id'] ); !!}
              <div class="row">
                <div class="input-field col m6 s6">
                  {!! Form::select('gst_percentage', $variants->tax_percentage, $billing->gst_percentage ?? '' , ['id' => 'gst_percentage', 'placeholder'=>'Select default tax percentage']) !!}
                  <label for="icon_prefix1">Default GST percentage %</label>
                </div>

                <div class="input-field col m6 s6">
                  <div class="input-field col s12">
                    <button class="btn cyan waves-effect waves-light right" type="submit" name="action" id="gst-submit-btn">Submit <i class="material-icons right">send</i></button>
                  </div>
                </div>

              </div>
            </form>

            <!-- users edit Info form ends -->
            <div class="divider mb-3"></div>
                <div class="row">
                  <h4 class="card-title">Additional Taxes</h4>
                    <div class="input-field col m12 s12">
                      <a href="javascript:" onclick="manageAdditionalTax(null)" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">Add <i class="material-icons right">account_balance_wallet</i></a>
                    </div>
                </div>

                <div class="row">
                  <div class="col s12">
                      <table id="data-table-taxes" class="display">
                        <thead>
                            <tr>
                              <th>No</th>
                              <th>Name</th>
                              <th>Tax Percentage</th>
                              <th>Details</th>
                              <th>Action</th>
                            </tr>
                        </thead>
                      </table>
                  </div>
                </div>
          </div>
          
          <div class="col s12" id="paymentTypes">
            <h4 class="card-title">Additional taxes</h4>
            <!-- users edit Info form start -->
              <a href="javascript:" onclick="managePaymentType(null)" class="btn waves-effect waves-light cyan breadcrumbs-btn right" type="submit" name="action">Add<i class="material-icons right">payment</i></a>
                <div class="row">
                  <div class="col s12">
                  <table id="data-table-payment-types" class="display">
                        <thead>
                            <tr>
                              <th>No</th>
                              <th>Name</th>
                              <th width="100px">Action</th>
                            </tr>
                        </thead>
                      </table>
                  </div>
                </div>

            <!-- users edit Info form ends -->
          </div>
        @endif
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
<!-- users edit ends -->
@include('additional-tax.manage')
@include('payment-type.manage')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('admin/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/js/dataTables.select.min.js')}}"></script>
@endsection


@push('page-scripts')
<script src="{{asset('admin/js/scripts/data-tables.js')}}"></script>
<script src="{{ asset('admin/js/common-script.js') }}"></script>

<!-- date-time-picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('admin/js/scripts/page-users.js')}}"></script>

<script>
  var table;

  $(function () {

    table = $('#data-table-payment-types').DataTable({
      bSearchable: true,
      pagination: true,
      pageLength: 10,
      responsive: true,
      searchDelay: 500,
      processing: true,
      serverSide: true,
      ajax: "{{ url(ROUTE_PREFIX.'/payment-types/lists') }}",
      columns: [
          {data: 'DT_RowIndex', orderable: false, searchable: false},
          {data: 'name', name: 'name', orderable: false,},
          {data: 'action', name: 'action', orderable: false, searchable: false, width:20},
      ]
    });

    taxTable = $('#data-table-taxes').DataTable({
      bSearchable: true,
      pagination: true,
      pageLength: 10,
      responsive: true,
      searchDelay: 500,
      processing: true,
      serverSide: true,
      ajax: "{{ url(ROUTE_PREFIX.'/additional-tax/lists') }}",
      columns: [
          {data: 'DT_RowIndex', orderable: false, searchable: false, width:10},
          {data: 'name', name: 'name', orderable: false,},
          {data: 'percentage', name: 'name', orderable: false, searchable: false},
          {data: 'information', name: 'name', orderable: false, searchable: false},
          {data: 'action', name: 'action', orderable: false, searchable: false, width:20},
      ]
    });

  });

  $('#billing_country_id').select2({ placeholder: "Please select country", allowClear: false });
  $('#billing_state_id').select2({ placeholder: "Please select state", allowClear: true });
  $('#billing_district_id').select2({ placeholder: "Please select district", allowClear: true });
  $('#currency').select2({ placeholder: "Please select currency", allowClear: true });

  $(document).on('change', '#billing_country_id', function () {

    if(this.value != 101){
        $("#submit-btn").prop('disabled', true);
        showErrorToaster("Currently not supported in your selected country!");
        $(".print-error-msg").show();
    }else{
      $("#submit-btn").prop('disabled', false);
      $(".print-error-msg").hide();
      $.ajax({
          type: 'POST', url: "{{ url(ROUTE_PREFIX.'/common/get-states-of-country') }}", data:{'country_id':this.value }, dataType: 'json',
          success: function(data) {
              var selectTerms = '<option value="">Please select state</option>';
              $.each(data.data, function(key, value) {
                selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
              });
              var select = $('#billing_state_id');
              select.empty().append(selectTerms);
              $('#billing_district_id').empty().trigger("change");
          }
      });
      $.ajax({
          type: 'POST', url: "{{ url(ROUTE_PREFIX.'/common/get-currencies') }}", data:{'country_id':this.value }, dataType: 'json',
          success: function(data) {
              var selectTerms = '<option value="">Please select currency</option>';
              $.each(data.data, function(key, value) {
                selectTerms += '<option value="' + value.id + '" >' + value.symbol + '</option>';
              });
              var select = $('#currency');
              select.empty().append(selectTerms);
          }
      });

      billing_currency
    }      
  });

  $(document).on('change', '#billing_state_id', function () {
    $.ajax({
        type: 'POST', url: "{{ url(ROUTE_PREFIX.'/common/get-districts-of-state') }}", data:{'state_id':this.value }, dataType: 'json',
        success: function(data) {
            var selectTerms = '<option value="">Please select district</option>';
            $.each(data.data, function(key, value) {
              selectTerms += '<option value="' + value.id + '" >' + value.name + '</option>';
            });
            var select = $('#billing_district_id');
            select.empty().append(selectTerms);
        }
    });
  });

  if ($("#storeBillingForm").length > 0) {
    var validator = $("#storeBillingForm").validate({ 
        rules: {
          company_name: {
            maxlength: 200,
            required: true,
          }
        },
        messages: { 
          company_name: {
            maxlength: "Length cannot be more than 200 characters",
            required: "Please enter company name",
          }
        },
        submitHandler: function (form) {
            id = $("#billing_id").val();
            userId      = "" == id ? "" : "/" + id;
            formMethod  = "" == id ? "POST" : "PUT";
            var forms   = $("#storeBillingForm");
            $.ajax({ url: "{{ url('/store/update/billing') }}" + userId, type: formMethod, processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    setTimeout(function () { 
                        window.location.href = "{{ url('store/billings')}}";                    
                    }, 2000);

                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
        }
    })
  }

  if ($("#storeGSTForm").length > 0) {
    var validator = $("#storeGSTForm").validate({ 
        rules: {
          gst_percentage: {
              required: true,
          }
        },
        messages: { 
          gst_percentage: {
            required: "Please select default GST",
          }
        },
        submitHandler: function (form) {
            id = $("#gst_billing_id").val();
            var forms   = $("#storeGSTForm");
            $.ajax({ url: "{{ url('/store/update/gst-billing') }}" , type: 'POST', processData: false, 
            data: forms.serialize(), dataType: "html",
            }).done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    showSuccessToaster(data.message);
                    // setTimeout(function () { 
                    //     window.location.href = "{{ url('store/billings')}}";                    
                    // }, 2000);

                }else{
                  showErrorToaster(data.message);
                  printErrorMsg(data.error);
                }
            });
        }
    })
  }

  function manageAdditionalTax(additionaltax_id){
    validator.resetForm();
    $('input').removeClass('error');
    if (additionaltax_id === null) {
        $("#additionalTaxForm")[0].reset();
        $('#additionalTaxForm').find("input[type=text]").val("");
        $("#additionaltax_id").val('');
        $("#additionaltaxFields .label-placeholder").show();
        $("#additionaltax-modal").modal("open");
    } else {
        $.ajax({url: "{{ url(ROUTE_PREFIX.'/additional-tax') }}/" + additionaltax_id + "/edit", type: "GET", dataType: "html"})
            .done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    $("#additionaltax_id").val(data.data.id);
                    $("#additionalTaxForm input[name=name]").val(data.data.name);
                    $("#additionalTaxForm input[name=percentage]").val(data.data.percentage);
                    $("#information").val(data.data.information);
                    $("#additionaltaxFields .label-placeholder").hide();
                    $("#additionaltax-modal").modal("open");
                }
            }).fail(function () {
                printErrorMsg("Please try again...", "error");
        });
    }
  }

  function managePaymentType(paymentType_id){
    validator.resetForm();
    $('input').removeClass('error');
    if (paymentType_id === null) {
        $("#paymentTypeForm")[0].reset();
        $('#paymentTypeForm').find("input[type=text]").val("");
        $("#paymentType_id").val('');
        $("#paymentTypeFields .label-placeholder").show();
        $('#paymentType-modal').modal('open');
    } else {
        $.ajax({url: "{{ url(ROUTE_PREFIX.'/payment-types') }}/" + paymentType_id + "/edit", type: "GET", dataType: "html"})
            .done(function (a) {
                var data = JSON.parse(a);
                if(data.flagError == false){
                    $("#paymentType_id").val(data.data.id);
                    $("#paymentTypeForm input[name=name]").val(data.data.name);

                    $("#paymentTypeFields .label-placeholder").hide();
                    $("#paymentType-modal").modal("open");
                }
            }).fail(function () {
                printErrorMsg("Please try again...", "error");
        });
    }
  }

  if ($("#additionalTaxForm").length > 0) {
    var validator = $("#additionalTaxForm").validate({ 
        rules: {
            name: {
                  required: true,
                  maxlength: 100,
            },
            percentage: {
                  required: true,
            },
            information: {
              maxlength: 200,
            }
        },
        messages: { 
          name: {
            required: "Please enter additional tax name",
            maxlength: "Length cannot be more than 100 characters",
          },
          percentage: {
            required: "Please enter tax percentage",
          },
          information: {
            maxlength: "Length cannot be more than 30 characters",
          }
        },
        submitHandler: function (form) {
          id = $("#additionaltax_id").val();
          additionaltax_id   = "" == id ? "" : "/" + id;
          formMethod  = "" == id ? "POST" : "PUT";
          var forms = $("#additionalTaxForm");
          $.ajax({ url: "{{ url(ROUTE_PREFIX.'/additional-tax') }}" + additionaltax_id, type: formMethod, processData: false, 
          data: forms.serialize(), dataType: "html",
          }).done(function (a) {
            var data = JSON.parse(a);
            if(data.flagError == false){
                showSuccessToaster(data.message);                
                $("#additionaltax-modal").modal("close");
                setTimeout(function () {
                  taxTable.ajax.reload();
                }, 2000);

            }else{
              showErrorToaster(data.message);
              printErrorMsg(data.error);
            }
          });
      }
    })
  }

  if ($("#paymentTypeForm").length > 0) {
    var validator = $("#paymentTypeForm").validate({ 
        rules: {
            name: {
                  required: true,
                  maxlength: 100,
            },
        },
        messages: { 
          name: {
            required: "Please enter payment type",
            maxlength: "Length cannot be more than 100 characters",
          },
        },
        submitHandler: function (form) {
          $('#paymentTypename-submit-btn').html('Please Wait...');
          $("#paymentTypename-submit-btn"). attr("disabled", true);
          id = $("#paymentType_id").val();
          paymentType_id   = "" == id ? "" : "/" + id;
          formMethod  = "" == id ? "POST" : "PUT";
          var forms = $("#paymentTypeForm");
          $.ajax({ url: "{{ url(ROUTE_PREFIX.'/payment-types') }}" + paymentType_id, type: formMethod, processData: false, 
          data: forms.serialize(), dataType: "html",
          }).done(function (a) {
            $('#paymentTypename-submit-btn').html('Submit <i class="material-icons right">send</i>');
            $("#paymentTypename-submit-btn"). attr("disabled", false);
            var data = JSON.parse(a);
            if(data.flagError == false){
                showSuccessToaster(data.message);                
                $("#paymentType-modal").modal("close");
                setTimeout(function () {
                  table.ajax.reload();
                }, 1000);

            }else{
              showErrorToaster(data.message);
              printErrorMsg(data.error);
            }
          });
      }
    })
  }

  function deleteAdditionalTax(b) {  
    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true,
			buttons: {
				cancel: 'No, Please!',
				delete: 'Yes, Delete It'
			}
		}).then(function (willDelete) {
			if (willDelete) {
			  $.ajax({url: "{{ url(ROUTE_PREFIX.'/additional-tax') }}/" + b, type: "DELETE", dataType: "html"})
            .done(function (a) {
              var data = JSON.parse(a);
              if(data.flagError == false){
                showSuccessToaster(data.message);          
                setTimeout(function () {
                  taxTable.ajax.reload();
                }, 1000);
              }else{
                showErrorToaster(data.message);
                printErrorMsg(data.error);
              }   
            }).fail(function () {
                showErrorToaster("Something went wrong!");
            });
			} 
		});
  }

  function deletePaymentTypes(b) {  
    swal({ title: "Are you sure?",icon: 'warning', dangerMode: true,
			buttons: {
				cancel: 'No, Please!',
				delete: 'Yes, Delete It'
			}
		}).then(function (willDelete) {
			if (willDelete) {
			  $.ajax({url: "{{ url(ROUTE_PREFIX.'/payment-types') }}/" + b, type: "DELETE", dataType: "html"})
            .done(function (a) {
              var data = JSON.parse(a);
              if(data.flagError == false){
                showSuccessToaster(data.message);          
                setTimeout(function () {
                  table.ajax.reload();
                }, 1000);
              }else{
                showErrorToaster(data.message);
                printErrorMsg(data.error);
              }   
            }).fail(function () {
                showErrorToaster("Something went wrong!");
            });
			} 
		});
  }

</script>
@endpush

