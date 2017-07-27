<div class="modal fade" id="commentModal" aria-labelledby="commentModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">@lang("admin/fulfillment.order_comment_popup_label")</h4>
            </div>
            {!! Form::open(array('url' => route('order.notes.create', [$order->id]), 'method' => 'POST', 'id' => 'create-order-note-modal')) !!}
                <div class="modal-body">
                        <div class="form-group">
                            {!! Form::textarea( 'notes', null, ['id' => 'add-comment-text-area', 'class' => 'form-control', 'placeholder' => trans('admin/fulfillment.order_placeholder_add_comment'), 'rows'=>'6'] ) !!}
                            <div class="error" id="add-comment-error"></div>
                            <input type="hidden" name="note_type" id="input-note-type" value="">
                            <input type="hidden" name="note_id" id="input-note-id" value="">
                            <input type="hidden" name="parent_note_id" id="input-note-parent-id" value="">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success pull-right">@lang("admin/fulfillment.button_submit")</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>