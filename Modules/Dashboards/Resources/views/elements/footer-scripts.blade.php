@if(!empty(config('dashboards.public.global.js')))
	@foreach(config('dashboards.public.global.js') as $script)
			<script src="{{ asset($script) }}" type="text/javascript"></script>
	@endforeach
@endif
@if(isset($action))
	@if(!empty(config('dashboards.public.pagelevel.js.'.$action)))
		@foreach(config('dashboards.public.pagelevel.js.'.$action) as $script)
				<script src="{{ asset($script) }}" type="text/javascript"></script>
		@endforeach
	@endif
@endif
