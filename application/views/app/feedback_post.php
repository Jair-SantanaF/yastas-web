<div class="container-fluid">
    <div class="row d-flex justify-content-center">

        <div class="col-11 mt-4">

            <div class="row mt-1">
            	<div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-sm-2 offset-md-2 offset-lg-2 offset-xl-2">
                    <div class="w-100 mt-2 border-top">
                        <div class="w-100 d-flex mt-4">
                            <div class="p-2">
                                <img style="min-width:150px; width: 150px; height: 150px" class="img-fluid rounded-circle" src="https://picsum.photos/200" alt="Generic placeholder image" id="photo_user">
                            </div>
                            <div class="w-100 d-flex pl-3">
                                <div class="align-self-center">
                                    <div class="w-100"><h1 id="name_user"></h1></div>
                                    <div class="w-100 py-3" id="job_name"></div>
                                </div>
                            </div>
                            <div class="p-2">
                                <i style="width: 50px" class="fas fa-sign-language fa-3x cursor-pointer" id="save_like"></i>
                                <div class="text-center" id="total_like">
                                </div>
                            </div>
                        </div>
                    </div>
            	</div>
            </div>
            <div class="row mt-2 pb-5">
                <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-sm-2 offset-md-2 offset-lg-2 offset-xl-2">
                    <div class="row d-flex border-top py-3">
                        <div class="col-6 d-none" id="media_path">
                        </div>
                        <div class="col-6 d-none" id="file_path">
                        </div>
                        <div class="pt-4" id="description">
                        </div>
                    </div>
                  </div>
            </div>

            <!--div class="row mt-2 mb-2">
                  
                  <div class="col col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 offset-sm-2 offset-md-2 offset-lg-2 offset-xl-2 d-flex justify-content-center">
                        <a href="<?php echo base_url(); ?>index.php/app/GiveFeedback" class="btn btn-lg button button-wrap">Dar feedback</a>
                  </div>

            </div-->
        </div>

    </div>
</div>

<script>
    var feedback_id = <?php echo $feedback_id?>;
</script>
<script src="<?php echo base_url()?>assets/js/app/feedback_post.js"></script>