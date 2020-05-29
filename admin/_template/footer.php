</div>
<!-- /.container-fluid -->
<footer class="footer text-center"> 2020 &copy; WoodWorth Cabinetry (v0.0.2)</footer>
</div>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
</div>
<!-- /#wrapper -->
<script>
    $(function() {
        $('#select-all').click(function() {
            $('#select-school').multiSelect('select_all');
            return false;
        });
        $('#deselect-all').click(function() {
            $('#select-school').multiSelect('deselect_all');
            return false;
        });
        // For select 2
        $(".erpSelect2").select2({
            placeholder: "-- Select --",
            templateResult: select2FormatState,
            templateSelection: select2FormatState
        });

        //lightbox
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox({
                wrapping: false
            });
        });

        //print
        $("#print").click(function() {
            var mode = 'iframe'; //popup
            var close = mode == "popup";
            var options = {
                mode: mode,
                popClose: close
            };
            $("div.printableArea").printArea(options);
        });
    });
</script>
</body>
</html>
<?php
echo ob_get_clean();
?>