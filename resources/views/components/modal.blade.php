<div class="modal" id="{{ $id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ $title }}</h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                    {{ $body }}
            </div>

            <div class="modal-footer">
                @isset($secondaryButton)
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-dismiss="modal"
                    >{{
                        $secondaryButton
                    }}</button>
                @endisset

                <button
                    type="button"
                    class="btn btn-primary"
                    data-dismiss="modal"
                >{{
                    $primaryButton
                }}</button>
            </div>
        </div>
    </div>
</div>
