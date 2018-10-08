<div class="m-subheader ">
	<div class="d-flex align-items-center">
		<div class="mr-auto">
			@isset($title)
				<h3 class="m-subheader__title m-subheader__title--separator">{{ $title }}</h3>
			@endisset
			<ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
				<li class="m-nav__item m-nav__item--home">
					<a href="{{ route('home') }}" class="m-nav__link m-nav__link--icon">
						<span class="m-nav__link-text">{{ __('Home') }}</span>
					</a>
				</li>
				@isset($breadcrumbs)
					@foreach($breadcrumbs as $breadcrumbTitle => $breadcrumbLink)
						<li class="m-nav__separator">-</li>
						<li class="m-nav__item" >
							<a href="{{ $breadcrumbLink }}" class="m-nav__link" >
								<span class="m-nav__link-text">{{$breadcrumbTitle}}</span>
							</a>
						</li>
					@endforeach
				@endisset
				@isset($title)
					<li class="m-nav__separator">-</li>
					<li class="m-nav__item" >
						<a href="{{ url()->full() }}" class="m-nav__link" >
							<span class="m-nav__link-text" style="font-weight: bold">{{ucfirst(kebab_case($title))}}</span>
						</a>
					</li>
				@endisset
			</ul>
		</div>
		@isset($right)
			{{ $right }}
		@endif
	</div>
</div>