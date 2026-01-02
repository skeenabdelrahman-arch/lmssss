<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jquery -->
<script src="{{ URL::asset('back/assets/js/jquery-3.3.1.min.js') }}"></script>
<!-- plugins-jquery -->
<script src="{{ URL::asset('back/assets/js/plugins-jquery.js') }}"></script>
<!-- plugin_path -->
<script type="text/javascript"> var plugin_path = '{{asset("back/assets/js")}}/' ;</script>


<!-- chart -->
<script src="{{ URL::asset('back/assets/js/chart-init.js') }}"></script>
<!-- calendar -->
<script src="{{ URL::asset('back/assets/js/calendar.init.js') }}"></script>
<!-- charts sparkline -->
<script src="{{ URL::asset('back/assets/js/sparkline.init.js') }}"></script>
<!-- charts morris -->
<script src="{{ URL::asset('back/assets/js/morris.init.js') }}"></script>
<!-- datepicker -->
<script src="{{ URL::asset('back/assets/js/datepicker.js') }}"></script>
<!-- sweetalert2 -->
<script src="{{ URL::asset('back/assets/js/sweetalert2.js') }}"></script>
<!-- toastr -->
@yield('js')
<script src="{{ URL::asset('back/assets/js/toastr.js') }}"></script>
<!-- validation -->
<script src="{{ URL::asset('back/assets/js/validation.js') }}"></script>
<!-- lobilist -->
<script src="{{ URL::asset('back/assets/js/lobilist.js') }}"></script>
<!-- custom -->
<script src="{{ URL::asset('back/assets/js/custom.js') }}"></script>

<!-- Mobile Sidebar Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.side-menu-fixed');
    
    if (sidebarToggle && sidebar) {
        // Toggle sidebar
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Close sidebar on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('show');
            }
        });
    }
});
</script>
