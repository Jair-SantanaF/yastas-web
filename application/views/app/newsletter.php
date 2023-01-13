<div class="container-fluid h-100">
    <div id="post" class="row"></div>
</div>
<div id="modal_comments" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comentarios</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center" id="comments"></div>
                <div class="row justify-content-center mt-3">
                    <div class="col-10 text-center">
                        <textarea id="comment_new" class="w-100" style="min-height: 100px; resize: none;" placeholder="Comentario"></textarea>
                        <button onclick="SaveComment()" class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url()?>assets/js/app/newsletter.js"></script>