<script>
    @if(auth()->check())
        var user = {!! json_encode(auth()->user()->toArray()) !!};
        var datefmt = {!! json_encode(auth()->user()->preference('DATE_FORMAT')) !!};
        var timefmt = {!! json_encode(auth()->user()->preference('TIME_FORMAT')) !!};

        @if(!is_null(auth()->user()->subscription))
            var cc_expire_within_a_month = {!! json_encode(auth()->user()->subscription->creditCardExpiringWithinMonthOrExpired()) !!};
        @endif
    @endif
</script>
