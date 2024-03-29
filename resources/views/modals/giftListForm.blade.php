<div id="ModalGiftListForm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!--Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Add new gift list </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            {{Form::open(['method'=>'POST', 'route'=>'giftList.create', 'id'=>'formGiftList'])}}
            <div class="modal-body">

                <div class="row form-group justify-content-center">
                    {{ Form::label('name', "Name :" , ['class' => 'col-md-4']) }}
                    <div class="col-md-8">
                        {{ Form::text('name', '', ['class' => 'form-control', 'id' => 'inputName']) }}
                    </div>
                </div>
                {{Form::hidden('id','',['id' => 'inputId'])}}

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default"
                        id="btnGiftListForm">
                    Create
                </button>
            </div>
            {{Form::close()}}

        </div>

    </div>
</div>
