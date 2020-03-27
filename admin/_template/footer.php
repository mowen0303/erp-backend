</div>
<!-- /.container-fluid -->
<footer class="footer text-center"> 2020 &copy; WoodWorth Cabinetry (v0.0.0.13)</footer>
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
        $(".select2").select2({
            placeholder: "-- Select --",
            templateResult: formatState,
            templateSelection: formatState
        });
        function formatState (opt) {
            if (!opt.id) {
                return opt.text;
            }
            var optimage = $(opt.element).data('image');
            if(!optimage){
                return opt.text;
            } else {
                var $opt = $(
                    '<span><img class="avatar avatar-30 img-rounded" src="' + optimage + '"/> ' + opt.text + '</span>'
                );
                return $opt;
            }
        };
        //lightbox
        $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
            event.preventDefault();
            return $(this).ekkoLightbox();
        });
    });
</script>
</body>
</html>
<?php
echo ob_get_clean();
?>