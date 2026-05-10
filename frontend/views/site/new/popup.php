
<div id="ModalForm" class="modal">
    <div class="modal-content contact-infotmation_input">
        <span class="close" onclick="closeModal()">&times;</span>

        <?
        $modelPopup = new \frontend\models\Mail();
        echo $this->context->renderPartial('/mail/index', [
            'model' => $modelPopup,
        ]);
        ?>

    </div>
</div>
<script>
    // Open the modal
    function openModal() {
        var modal = document.getElementById("ModalForm");
        modal.style.display = "flex";
        setTimeout(function () {
            modal.classList.add("show");
        }, 10);
    }

    // Close the modal
    function closeModal() {
        var modal = document.getElementById("ModalForm");
        modal.classList.remove("show");
        setTimeout(function () {
            modal.style.display = "none";
        }, 200);
    }
</script>
