<!--Container-End-->
<script src="https://code.jquery.com/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="{{ asset('asset/bootstrap/js/bootstrap.min.js') }}"></script>
<!--chart-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

<!--Spline chart-->
<script src="{{ asset('new_assets/js/jquery.pista.js') }}" type="text/javascript"></script>
<script type="text/javascript" src="{{ asset('new_assets/js/jquery.circliful.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('new_assets/js/script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-hover-dropdown/2.2.1/bootstrap-hover-dropdown.js" type="text/javascript"></script>
<script>
    $('.dropdown-toggle').dropdown()
</script>

@yield('scripts')