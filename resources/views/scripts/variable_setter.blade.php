<script>
    @if(auth()->check())
        var user = {!! json_encode(auth()->user()->toArray()) !!};
    @endif
</script>