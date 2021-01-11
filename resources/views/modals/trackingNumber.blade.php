<div id="ModalTrackingNumber" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!--Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Tracking number </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            {{Form::open(['method'=>'POST', 'route'=>'command.trackingNumber', 'id'=>'formTrackingNumber'])}}
            <div class="modal-body">

                <div class="row form-group justify-content-center">
                    {{ Form::label('trackingNumber', "Tracking number :" , ['class' => 'col-md-4']) }}
                    <div class="col-md-8">
                        {{ Form::text('trackingNumber', '', ['class' => 'form-control', 'id' => 'inputTrackingNumber']) }}
                    </div>
                </div>
                {{Form::hidden('id','',['id' => 'inputId'])}}

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default"
                        id="btnFormTrackingNumber">
                    Create
                </button>
            </div>
            {{Form::close()}}

        </div>

    </div>
</div>
