<div id="ModalCheckOrderMKM" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!--Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Order number </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            {{Form::open(['method'=>'POST', 'route'=>'command.checkMKM', 'id'=>'formCheckOrderMKM'])}}
            <div class="modal-body">

                <div class="row form-group justify-content-center">
                    {{ Form::label('idOrderMKM', "MKM order number :" , ['class' => 'col-md-4']) }}
                    <div class="col-md-8">
                        {{ Form::text('idOrderMKM', '', ['class' => 'form-control', 'id' => 'inputIdOrderMKM']) }}
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default"
                        id="btnFormCheckOrderMKM">
                    Create
                </button>
            </div>
            {{Form::close()}}

        </div>

    </div>
</div>
