<!-- Main Footer -->
 <footer class="main-footer" style="position: fixed;">
   <!-- To the right -->
   <div class="pull-right hidden-xs">
     ibrahim@karakuzu.info
   </div>
   <!-- Default to the left -->
   <strong>Copyright &copy; 2022 <a href="https:\\karakuzu.info" target="_blank">Karakuzu Bilişim</a></strong> Tüm Hakları Saklıdır.
 </footer>

 <!-- Control Sidebar -->

 <!-- /.control-sidebar -->
 <!-- Add the sidebar's background. This div must be placed
 immediately after the control sidebar -->
 <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<script>
 $(function () {
   //Initialize Select2 Elements
   $('.select2').select2()

 })
</script>
<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="{{ URL::asset('assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/bower_components/jquery-ui/jquery-ui.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ URL::asset('assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ URL::asset('assets/dist/js/adminlte.min.js') }}"></script>

<script src="{{ URL::asset('assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>




</body>
</html>
