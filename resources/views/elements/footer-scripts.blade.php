<script type="text/javascript">
var prefix_subdirectory = {!! json_encode(env('PREFIX_SUBDIRECTORY')) !!};
</script>
@if(!empty(config('dz.public.global.js')))
	@foreach(config('dz.public.global.js') as $script)
			<script src="{{ asset($script) }}" type="text/javascript"></script>
	@endforeach
@endif
@if(isset($action))
	@if(!empty(config('dz.public.pagelevel.js.'.$action)))
		@foreach(config('dz.public.pagelevel.js.'.$action) as $script)
				<script src="{{ asset($script) }}" type="text/javascript"></script>
		@endforeach
	@endif
@endif
