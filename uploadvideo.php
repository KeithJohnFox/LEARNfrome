<?php
require_once("includes/header.php");
require_once("includes/classes/VideoDetailsFormProvider.php");
?>

<div id="mainContainer" style="
    padding-top: 74px;">
    <div class="uploadContainer">
        <?php
            $uploadform = new VideoDetailsFormProvider($con);
            echo $uploadform->createUploadForm();
        ?>
    </div>
</div>

<!-- Script to run loading icon -->
<script>
$("form").submit(function() {
    $("#loadspinner").modal("show");
});
</script>
<div class="wrapper">
    <!-- Modal Box Display uploading tutorail message with loading Spinner -->
    <div class="modal fade" id="loadspinner" tabindex="-1" role="dialog" aria-labelledby="loadspinner" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    Your tutorial is uploading! this may take a moment...
                    <img src="assets/images/icons/spinner.gif" alt="Loading..">
                </div>
            </div>
        </div>
    </div>

