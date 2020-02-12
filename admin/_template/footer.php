</div>
<!-- /.container-fluid -->
<footer class="footer text-center"> 2020 &copy; WoodWorth Cabinetry</footer>
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
        $(".select2").select2();
    });
</script>
</body>
</html>
<?php
echo ob_get_clean();
?>