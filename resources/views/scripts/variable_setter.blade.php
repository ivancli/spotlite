<script>
    @if(auth()->check())
        var user = {!! json_encode(auth()->user()->toArray()) !!};

        @if(!is_null(auth()->user()->validSubscription()))
            var cc_expire_within_a_month = {!! json_encode(auth()->user()->validSubscription()->creditCardExpiringWithinMonthOrExpired()) !!};
        @endif
    @endif
</script>
