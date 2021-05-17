<div id="business-types-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">{{ $page->title ?? ''}} Form</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger alert-messages print-error-msg" style="display:none;"><ul></ul></div>
                <div class="alert alert-success fade alert-messages print-success-msg" style="display:none;"></div>
            <form id="{{$page->entity}}Form" name="{{$page->entity}}Form" role="form" method="POST" action="" class="ajax-submit">
                {{ csrf_field() }}
                {!! Form::hidden('additionaltax_id', '' , ['id' => 'additionaltax_id'] ); !!}
                <div class="card-body">
                  <div class="form-group">
                    {!! Form::label('name', 'Tax name *', ['class' => '']) !!}
                    {!! Form::text('name', '', ['class' => 'form-control', 'id' => 'name', 'placeholder'=>'Enter Tax name']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::label('percentage', 'Tax percentage *', ['class' => '']) !!}
                    {!! Form::text('percentage', '', ['class' => 'form-control check_numeric', 'id' => 'percentage', 'placeholder'=>'Enter Tax percentage']) !!}
                  </div>

                  <div class="form-group ">
                    {!! Form::label('information', 'Additional information. ', ['class' => 'col-sm-6 col-form-label text-alert']) !!}
                    {!! Form::textarea('information', '', ['class' => 'form-control','placeholder'=>'Additional information','rows'=>3]) !!}                       
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="modal-footer">					
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button class="btn btn-success ajax-submit">Submit</button>
                </div>
            </form>
            </div>

          </div>
          <!-- /.modal-content -->
    </div>
</div>