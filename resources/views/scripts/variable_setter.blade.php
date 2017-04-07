<script>
    @if(auth()->check())
        var user = {!! \App\Models\User::findOrFail(auth()->user()->getKey())->toJSON() !!};
        var datefmt = {!! json_encode(auth()->user()->preference('DATE_FORMAT')) !!};
        var timefmt = {!! json_encode(auth()->user()->preference('TIME_FORMAT')) !!};

        @if(!is_null(auth()->user()->subscription))
            var cc_expire_within_a_month = {!! json_encode(auth()->user()->subscription->creditCardExpiringWithinMonthOrExpired()) !!};
        @endif
    @endif
</script>
