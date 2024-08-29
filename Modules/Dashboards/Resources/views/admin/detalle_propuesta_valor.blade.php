{{-- Extends layout --}}
@extends('layout.template')

@section('content')
<div class="container-fluid">
	<div class="page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('principal_index') }}">Propuesta de valor</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Detalle porpuesta de valor</a></li>
		</ol>
	</div>
	<!-- Add Order -->
	<div class="modal fade" id="addOrderModalside">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Add Event</h5>
					<button type="button" class="close" data-dismiss="modal"><span>&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form>
						<div class="form-group">
							<label class="text-black font-w500">Event Name</label>
							<input type="text" class="form-control">
						</div>
						<div class="form-group">
							<label class="text-black font-w500">Event Date</label>
							<input type="date" class="form-control">
						</div>
						<div class="form-group">
							<label class="text-black font-w500">Description</label>
							<input type="text" class="form-control">
						</div>
						<div class="form-group">
							<button type="button" class="btn btn-primary">Create</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xl-9 col-xxl-8">
			<div class="row">
				<div class="col-xl-12">
					<div class="card event-detail-bx overflow-hidden">
						
						<div class="card-body">
							<div class="d-flex flex-wrap align-items-center mb-4">
								<h2 class="text-black col-xl-6 p-0 mr-auto title mb-3">Postobón</h2>
								
								<div class="d-flex align-items-center">
									<a href="javascript:void(0)" class="btn btn-info light mr-3">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>

										</i>Luminometría</a>
								<!-- 	<a href="javascript:void(0)" class="share-icon mr-3">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M11 2H6C3.791 2 2 3.791 2 6V18C2 20.209 3.791 22 6 22H18C20.209 22 22 20.209 22 18C22 15.729 22 13 22 13C22 12.448 21.552 12 21 12C20.448 12 20 12.448 20 13V18C20 19.104 19.104 20 18 20C14.67 20 9.329 20 6 20C4.895 20 4 19.104 4 18C4 14.67 4 9.329 4 6C4 4.895 4.895 4 6 4H11C11.552 4 12 3.552 12 3C12 2.448 11.552 2 11 2ZM18.586 4H15C14.448 4 14 3.552 14 3C14 2.448 14.448 2 15 2H21C21.552 2 22 2.448 22 3V9C22 9.552 21.552 10 21 10C20.448 10 20 9.552 20 9V5.414L12.707 12.707C12.317 13.097 11.683 13.097 11.293 12.707C10.902 12.317 10.902 11.683 11.293 11.293L18.586 4Z" fill="#FE634E"/>
										</svg>
									</a> -->
									<div class="dropdown">
										<div class="share-icon" role="button" data-toggle="dropdown" aria-expanded="false">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="#FE634E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="#FE634E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="#FE634E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</div>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item" href="javascript:void(0);">Agregar observación</a>
												<a class="dropdown-item" href="javascript:void(0);">Orden de servicio</a>
											<!--<a class="dropdown-item" href="javascript:void(0);">Delete</a> -->
										</div>
									</div>
								</div>
							</div>
							<div class="row">
							
								<div class="col-sm-4 mb-3">
									<div class="media bg-light p-3 rounded align-items-center">	
										<svg class="mr-4" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
											<g clip-path="url(#clip0)">
											<path d="M21 3H20C20 2.20435 19.6839 1.44129 19.1213 0.87868C18.5587 0.31607 17.7956 0 17 0C16.2044 0 15.4413 0.31607 14.8787 0.87868C14.3161 1.44129 14 2.20435 14 3H10C10 2.20435 9.68393 1.44129 9.12132 0.87868C8.55871 0.316071 7.79565 4.47035e-08 7 4.47035e-08C6.20435 4.47035e-08 5.44129 0.316071 4.87868 0.87868C4.31607 1.44129 4 2.20435 4 3H3C2.20435 3 1.44129 3.31607 0.87868 3.87868C0.31607 4.44129 0 5.20435 0 6L0 21C0 21.7956 0.31607 22.5587 0.87868 23.1213C1.44129 23.6839 2.20435 24 3 24H21C21.7956 24 22.5587 23.6839 23.1213 23.1213C23.6839 22.5587 24 21.7956 24 21V6C24 5.20435 23.6839 4.44129 23.1213 3.87868C22.5587 3.31607 21.7956 3 21 3ZM3 5H4C4 5.79565 4.31607 6.55871 4.87868 7.12132C5.44129 7.68393 6.20435 8 7 8C7.26522 8 7.51957 7.89464 7.70711 7.70711C7.89464 7.51957 8 7.26522 8 7C8 6.73478 7.89464 6.48043 7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 5.89464 6.29289 5.70711C6.10536 5.51957 6 5.26522 6 5V3C6 2.73478 6.10536 2.48043 6.29289 2.29289C6.48043 2.10536 6.73478 2 7 2C7.26522 2 7.51957 2.10536 7.70711 2.29289C7.89464 2.48043 8 2.73478 8 3V4C8 4.26522 8.10536 4.51957 8.29289 4.70711C8.48043 4.89464 8.73478 5 9 5H14C14 5.79565 14.3161 6.55871 14.8787 7.12132C15.4413 7.68393 16.2044 8 17 8C17.2652 8 17.5196 7.89464 17.7071 7.70711C17.8946 7.51957 18 7.26522 18 7C18 6.73478 17.8946 6.48043 17.7071 6.29289C17.5196 6.10536 17.2652 6 17 6C16.7348 6 16.4804 5.89464 16.2929 5.70711C16.1054 5.51957 16 5.26522 16 5V3C16 2.73478 16.1054 2.48043 16.2929 2.29289C16.4804 2.10536 16.7348 2 17 2C17.2652 2 17.5196 2.10536 17.7071 2.29289C17.8946 2.48043 18 2.73478 18 3V4C18 4.26522 18.1054 4.51957 18.2929 4.70711C18.4804 4.89464 18.7348 5 19 5H21C21.2652 5 21.5196 5.10536 21.7071 5.29289C21.8946 5.48043 22 5.73478 22 6V10H2V6C2 5.73478 2.10536 5.48043 2.29289 5.29289C2.48043 5.10536 2.73478 5 3 5ZM21 22H3C2.73478 22 2.48043 21.8946 2.29289 21.7071C2.10536 21.5196 2 21.2652 2 21V12H22V21C22 21.2652 21.8946 21.5196 21.7071 21.7071C21.5196 21.8946 21.2652 22 21 22Z" fill="#FE634E"/>
											<path d="M12 16C12.5523 16 13 15.5523 13 15C13 14.4477 12.5523 14 12 14C11.4477 14 11 14.4477 11 15C11 15.5523 11.4477 16 12 16Z" fill="#FE634E"/>
											<path d="M18 16C18.5523 16 19 15.5523 19 15C19 14.4477 18.5523 14 18 14C17.4477 14 17 14.4477 17 15C17 15.5523 17.4477 16 18 16Z" fill="#FE634E"/>
											<path d="M6 16C6.55228 16 7 15.5523 7 15C7 14.4477 6.55228 14 6 14C5.44771 14 5 14.4477 5 15C5 15.5523 5.44771 16 6 16Z" fill="#FE634E"/>
											<path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" fill="#FE634E"/>
											<path d="M18 20C18.5523 20 19 19.5523 19 19C19 18.4477 18.5523 18 18 18C17.4477 18 17 18.4477 17 19C17 19.5523 17.4477 20 18 20Z" fill="#FE634E"/>
											<path d="M6 20C6.55228 20 7 19.5523 7 19C7 18.4477 6.55228 18 6 18C5.44771 18 5 18.4477 5 19C5 19.5523 5.44771 20 6 20Z" fill="#FE634E"/>
											</g>
											<defs>
											<clipPath id="clip0">
											<rect width="24" height="24" fill="white"/>
											</clipPath>
											</defs>
										</svg>
										<div class="media-body">
											<span class="fs-12 d-block mb-1">Fecha</span>
											<span class="fs-16 text-black">Domingo, 24 Oct 2021</span>
										</div>
									</div>
								</div>
								<div class="col-sm-4 mb-3">
									<div class="media bg-light p-3 rounded align-items-center">	
										<svg class="mr-4" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
											<g clip-path="">
											<path d="M27.5713 13.4286C27.5713 22.4286 15.9999 30.1428 15.9999 30.1428C15.9999 30.1428 4.42847 22.4286 4.42847 13.4286C4.42847 10.3596 5.6476 7.41638 7.81766 5.24632C9.98772 3.07625 12.931 1.85712 15.9999 1.85712C19.0688 1.85712 22.0121 3.07625 24.1821 5.24632C26.3522 7.41638 27.5713 10.3596 27.5713 13.4286Z" stroke="#FE634E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M15.9997 17.2857C18.13 17.2857 19.8569 15.5588 19.8569 13.4286C19.8569 11.2983 18.13 9.57141 15.9997 9.57141C13.8695 9.57141 12.1426 11.2983 12.1426 13.4286C12.1426 15.5588 13.8695 17.2857 15.9997 17.2857Z" stroke="#FE634E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
											</g>
											<defs>
											<clipPath id="clip3">
											<rect width="30.8571" height="30.8571" fill="white" transform="translate(0.571289 0.571411)"/>
											</clipPath>
											</defs>
										</svg>
										<div class="media-body">
											<span class="fs-12 d-block mb-1">Sucursal</span>
											<span class="fs-16 text-black">El Dorado</span>
										</div>
									</div>
								</div>
								<div class="col-sm-4 mb-3">
									<div class="media bg-light p-3 rounded align-items-center">	
										<svg class="mr-2" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M6.07438 25H7.95454V22.6464C11.8595 22.302 14 19.6039 14 16.8197C14 12.7727 10.8471 11.9977 7.95454 11.3088V5.10907C9.34297 5.4535 10.1529 6.5155 10.2686 7.66361H13.7975C13.5372 4.42021 11.281 2.61194 7.95454 2.32492V0H6.07438V2.35362C2.4876 2.66935 0 4.87945 0 8.09415C0 12.1412 3.18182 12.9449 6.07438 13.6625V19.977C4.45455 19.69 3.64463 18.628 3.52893 17.1929H0C0 20.4363 2.54545 22.3594 6.07438 22.6751V25ZM10.6736 16.992C10.6736 18.4845 9.69008 19.69 7.95454 19.977V14.1504C9.51653 14.6383 10.6736 15.3559 10.6736 16.992ZM3.35537 7.92193C3.35537 6.17107 4.48347 5.22388 6.07438 5.02296V10.8209C4.5124 10.333 3.35537 9.58668 3.35537 7.92193Z" fill="#FE634E"/>
											
										</svg>
				
										
										<div class="media-body">
											<span class="fs-12 d-block mb-1">Experto L&D</span>
											<span class="fs-16 text-black">Cristian Villada</span>
										</div>
									</div>
								</div>

							</div>
							<div class="row">
							
								<div class="col-sm-4 mb-3">
									<div class="media bg-light p-3 rounded align-items-center">	
										<svg class="mr-4" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
											<g clip-path="url(#clip0)">
											<path d="M21 3H20C20 2.20435 19.6839 1.44129 19.1213 0.87868C18.5587 0.31607 17.7956 0 17 0C16.2044 0 15.4413 0.31607 14.8787 0.87868C14.3161 1.44129 14 2.20435 14 3H10C10 2.20435 9.68393 1.44129 9.12132 0.87868C8.55871 0.316071 7.79565 4.47035e-08 7 4.47035e-08C6.20435 4.47035e-08 5.44129 0.316071 4.87868 0.87868C4.31607 1.44129 4 2.20435 4 3H3C2.20435 3 1.44129 3.31607 0.87868 3.87868C0.31607 4.44129 0 5.20435 0 6L0 21C0 21.7956 0.31607 22.5587 0.87868 23.1213C1.44129 23.6839 2.20435 24 3 24H21C21.7956 24 22.5587 23.6839 23.1213 23.1213C23.6839 22.5587 24 21.7956 24 21V6C24 5.20435 23.6839 4.44129 23.1213 3.87868C22.5587 3.31607 21.7956 3 21 3ZM3 5H4C4 5.79565 4.31607 6.55871 4.87868 7.12132C5.44129 7.68393 6.20435 8 7 8C7.26522 8 7.51957 7.89464 7.70711 7.70711C7.89464 7.51957 8 7.26522 8 7C8 6.73478 7.89464 6.48043 7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6C6.73478 6 6.48043 5.89464 6.29289 5.70711C6.10536 5.51957 6 5.26522 6 5V3C6 2.73478 6.10536 2.48043 6.29289 2.29289C6.48043 2.10536 6.73478 2 7 2C7.26522 2 7.51957 2.10536 7.70711 2.29289C7.89464 2.48043 8 2.73478 8 3V4C8 4.26522 8.10536 4.51957 8.29289 4.70711C8.48043 4.89464 8.73478 5 9 5H14C14 5.79565 14.3161 6.55871 14.8787 7.12132C15.4413 7.68393 16.2044 8 17 8C17.2652 8 17.5196 7.89464 17.7071 7.70711C17.8946 7.51957 18 7.26522 18 7C18 6.73478 17.8946 6.48043 17.7071 6.29289C17.5196 6.10536 17.2652 6 17 6C16.7348 6 16.4804 5.89464 16.2929 5.70711C16.1054 5.51957 16 5.26522 16 5V3C16 2.73478 16.1054 2.48043 16.2929 2.29289C16.4804 2.10536 16.7348 2 17 2C17.2652 2 17.5196 2.10536 17.7071 2.29289C17.8946 2.48043 18 2.73478 18 3V4C18 4.26522 18.1054 4.51957 18.2929 4.70711C18.4804 4.89464 18.7348 5 19 5H21C21.2652 5 21.5196 5.10536 21.7071 5.29289C21.8946 5.48043 22 5.73478 22 6V10H2V6C2 5.73478 2.10536 5.48043 2.29289 5.29289C2.48043 5.10536 2.73478 5 3 5ZM21 22H3C2.73478 22 2.48043 21.8946 2.29289 21.7071C2.10536 21.5196 2 21.2652 2 21V12H22V21C22 21.2652 21.8946 21.5196 21.7071 21.7071C21.5196 21.8946 21.2652 22 21 22Z" fill="#FE634E"/>
											<path d="M12 16C12.5523 16 13 15.5523 13 15C13 14.4477 12.5523 14 12 14C11.4477 14 11 14.4477 11 15C11 15.5523 11.4477 16 12 16Z" fill="#FE634E"/>
											<path d="M18 16C18.5523 16 19 15.5523 19 15C19 14.4477 18.5523 14 18 14C17.4477 14 17 14.4477 17 15C17 15.5523 17.4477 16 18 16Z" fill="#FE634E"/>
											<path d="M6 16C6.55228 16 7 15.5523 7 15C7 14.4477 6.55228 14 6 14C5.44771 14 5 14.4477 5 15C5 15.5523 5.44771 16 6 16Z" fill="#FE634E"/>
											<path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" fill="#FE634E"/>
											<path d="M18 20C18.5523 20 19 19.5523 19 19C19 18.4477 18.5523 18 18 18C17.4477 18 17 18.4477 17 19C17 19.5523 17.4477 20 18 20Z" fill="#FE634E"/>
											<path d="M6 20C6.55228 20 7 19.5523 7 19C7 18.4477 6.55228 18 6 18C5.44771 18 5 18.4477 5 19C5 19.5523 5.44771 20 6 20Z" fill="#FE634E"/>
											</g>
											<defs>
											<clipPath id="clip0">
											<rect width="24" height="24" fill="white"/>
											</clipPath>
											</defs>
										</svg>
										<div class="media-body">
											<span class="fs-12 d-block mb-1">Recibida por</span>
											<span class="fs-16 text-black">Viviana Lopez</span>
										</div>
									</div>
								</div>
								<div class="col-sm-4 mb-3">
									<div class="media bg-light p-3 rounded align-items-center">	
										<svg class="mr-4" width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
											<g clip-path="">
											<path d="M27.5713 13.4286C27.5713 22.4286 15.9999 30.1428 15.9999 30.1428C15.9999 30.1428 4.42847 22.4286 4.42847 13.4286C4.42847 10.3596 5.6476 7.41638 7.81766 5.24632C9.98772 3.07625 12.931 1.85712 15.9999 1.85712C19.0688 1.85712 22.0121 3.07625 24.1821 5.24632C26.3522 7.41638 27.5713 10.3596 27.5713 13.4286Z" stroke="#FE634E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
											<path d="M15.9997 17.2857C18.13 17.2857 19.8569 15.5588 19.8569 13.4286C19.8569 11.2983 18.13 9.57141 15.9997 9.57141C13.8695 9.57141 12.1426 11.2983 12.1426 13.4286C12.1426 15.5588 13.8695 17.2857 15.9997 17.2857Z" stroke="#FE634E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
											</g>
											<defs>
											<clipPath id="clip3">
											<rect width="30.8571" height="30.8571" fill="white" transform="translate(0.571289 0.571411)"/>
											</clipPath>
											</defs>
										</svg>
										<div class="media-body">
											<span class="fs-12 d-block mb-1">Ubicación</span>
											<span class="fs-16 text-black">Avenida 45 #23 -47, Bogotá</span>
										</div>
									</div>
								</div>
								<div class="col-sm-4 mb-3">
									<div class="media bg-light p-3 rounded align-items-center">	
										<svg class="mr-2" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M6.07438 25H7.95454V22.6464C11.8595 22.302 14 19.6039 14 16.8197C14 12.7727 10.8471 11.9977 7.95454 11.3088V5.10907C9.34297 5.4535 10.1529 6.5155 10.2686 7.66361H13.7975C13.5372 4.42021 11.281 2.61194 7.95454 2.32492V0H6.07438V2.35362C2.4876 2.66935 0 4.87945 0 8.09415C0 12.1412 3.18182 12.9449 6.07438 13.6625V19.977C4.45455 19.69 3.64463 18.628 3.52893 17.1929H0C0 20.4363 2.54545 22.3594 6.07438 22.6751V25ZM10.6736 16.992C10.6736 18.4845 9.69008 19.69 7.95454 19.977V14.1504C9.51653 14.6383 10.6736 15.3559 10.6736 16.992ZM3.35537 7.92193C3.35537 6.17107 4.48347 5.22388 6.07438 5.02296V10.8209C4.5124 10.333 3.35537 9.58668 3.35537 7.92193Z" fill="#FE634E"/>
										</svg>
										<div class="media-body">
											<span class="fs-12 d-block mb-1">Costo</span>
											<span class="fs-16 text-black">$124,000</span>
										</div>
									</div>
								</div>

							</div>
							<h4 class="fs-20 text-black font-w600">Descripción del evento</h4>
							<p class="fs-14 mb-0">
								Se realiza Luminometria en el lavado de manos a la señorita Bibiana Peñaloza Arias identificada con número de cédula 40.612.470 de Florencia, adicionalmente se realiza Luminometria en el área de cocina en el pdv especialmente en el área de corte de verduras tabla para picar
							</p>
						</div>
					</div>
				</div>
				<div class="col-xl-12">
					<div class="card">
						<div class="card-body "> 

							<div class="d-flex flex-wrap align-items-center mb-4">
								<h4 class="fs-20 text-black font-w600">SUPERFICIE/EQUIPOS</h4>
							</div>
							
							<div class="table-responsive fs-14">
								<table class="table">
									<thead>
										<tr>
											<th><strong>Superficie/Equipos</strong></th>
											<th><strong>Area</strong></th>
											<th><strong>Antes</strong></th>
											<th><strong>Despues</strong></th>
											<th><strong>Descripción</strong></th>
											<th><strong>Imagen</strong></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Tabla de picar</td>
											<td>Cocina</td>
											<td>32</td>
											<td>5</td>
											<td>Concentración 11ml</td>
											<td>NO</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-12">
					<div class="card">
						<div class="card-body">
							<div class="d-flex flex-wrap align-items-center mb-4">
								<h4 class="fs-20 text-black font-w600">Manos</h4>
							</div>
							<div class="table-responsive fs-14">
								<table class="table">
									<thead>
										<tr>
											<th><strong>Colaborador</strong></th>
											<th><strong>Cargo</strong></th>
											<th><strong>Área</strong></th>
											<th><strong>Antes</strong></th>
											<th><strong>Después</strong></th>
											<th><strong>Descripción</strong></th>
											<th><strong>Imagen</strong></th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Viviana Perez</td>
											<td>Cocinera</td>
											<td>Cocina</td>
											<td>10</td>
											<td>0</td>
											<td>No se identifica carga organica.</td>
											<td>NO</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
		
		<div class="col-xl-3 col-xxl-4">
			<div class="row">
				<div class="col-xl-12 col-sm-6">
					<div class="card">
						<div class="card-header border-0 pb-0">
							<h4 class="fs-20 text-black">Cumplimiento del protocolo L&D</h4>
						</div>
						<div class="card-body pt-3">
							<div class="d-flex justify-content-between align-items-center">	
								<span class="fs-14">2021</span>
								<span class="text-black fs-18 font-w500">14 url prom</span>
							</div>
							<div id="radialBar"></div>
								<div id="apexcharts90qq3omp" class="apexcharts-canvas apexcharts90qq3omp apexcharts-theme-light" style="width: 299px; height: 250.7px;"><svg id="SvgjsSvg1096" width="299" height="250.7" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG1098" class="apexcharts-inner apexcharts-graphical" transform="translate(37.5, 0)"><defs id="SvgjsDefs1097"><clipPath id="gridRectMask90qq3omp"><rect id="SvgjsRect1100" width="232" height="250" x="-3" y="-1" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="gridRectMarkerMask90qq3omp"><rect id="SvgjsRect1101" width="230" height="252" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><linearGradient id="SvgjsLinearGradient1106" x1="1" y1="0" x2="0" y2="1"><stop id="SvgjsStop1107" stop-opacity="1" stop-color="rgba(242,242,242,1)" offset="0"></stop><stop id="SvgjsStop1108" stop-opacity="1" stop-color="rgba(206,206,206,1)" offset="0.5"></stop><stop id="SvgjsStop1109" stop-opacity="1" stop-color="rgba(206,206,206,1)" offset="0.65"></stop><stop id="SvgjsStop1110" stop-opacity="1" stop-color="rgba(242,242,242,1)" offset="0.91"></stop></linearGradient><linearGradient id="SvgjsLinearGradient1118" x1="1" y1="0" x2="0" y2="1"><stop id="SvgjsStop1119" stop-opacity="1" stop-color="rgba(254,99,78,1)" offset="0"></stop><stop id="SvgjsStop1120" stop-opacity="1" stop-color="rgba(216,84,66,1)" offset="0.5"></stop><stop id="SvgjsStop1121" stop-opacity="1" stop-color="rgba(216,84,66,1)" offset="0.65"></stop><stop id="SvgjsStop1122" stop-opacity="1" stop-color="rgba(254,99,78,1)" offset="0.91"></stop></linearGradient></defs><g id="SvgjsG1102" class="apexcharts-radialbar"><g id="SvgjsG1103"><g id="SvgjsG1104" class="apexcharts-tracks"><g id="SvgjsG1105" class="apexcharts-radialbar-track apexcharts-track" rel="1"><path id="apexcharts-radialbarTrack-0" d="M 113 45.328658536585365 A 78.67134146341463 78.67134146341463 0 1 1 112.98626926071472 45.328659734818416" fill="none" fill-opacity="1" stroke="rgba(242,242,242,0.85)" stroke-opacity="1" stroke-linecap="round" stroke-width="20.243426829268294" stroke-dasharray="0" class="apexcharts-radialbar-area" data:pathOrig="M 113 45.328658536585365 A 78.67134146341463 78.67134146341463 0 1 1 112.98626926071472 45.328659734818416"></path></g></g><g id="SvgjsG1112"><g id="SvgjsG1117" class="apexcharts-series apexcharts-radial-series" seriesName="" rel="1" data:realIndex="0"><path id="SvgjsPath1123" d="M 113 45.328658536585365 A 78.67134146341463 78.67134146341463 0 0 1 132.03232005619802 200.3344664023508" fill="none" fill-opacity="0.85" stroke="url(#SvgjsLinearGradient1118)" stroke-opacity="1" stroke-linecap="round" stroke-width="20.869512195121953" stroke-dasharray="0" class="apexcharts-radialbar-area apexcharts-radialbar-slice-0" data:angle="166" data:value="46" index="0" j="0" data:pathOrig="M 113 45.328658536585365 A 78.67134146341463 78.67134146341463 0 0 1 132.03232005619802 200.3344664023508"></path></g><circle id="SvgjsCircle1113" r="63.54962804878049" cx="113" cy="124" class="apexcharts-radialbar-hollow" fill="#ffffff"></circle><g id="SvgjsG1114" class="apexcharts-datalabels-group" transform="translate(0, 0) scale(1)" style="opacity: 1;"><text id="SvgjsText1115" font-family="Helvetica, Arial, sans-serif" x="113" y="124" text-anchor="middle" dominant-baseline="auto" font-size="16px" font-weight="400" fill="#008ffb" class="apexcharts-text apexcharts-datalabel-label" style="font-family: Helvetica, Arial, sans-serif;"></text><text id="SvgjsText1116" font-family="Helvetica, Arial, sans-serif" x="113" y="140" text-anchor="middle" dominant-baseline="auto" font-size="24px" font-weight="400" fill="black" class="apexcharts-text apexcharts-datalabel-value" style="font-family: Helvetica, Arial, sans-serif;">46%</text></g></g></g></g><line id="SvgjsLine1124" x1="0" y1="0" x2="226" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine1125" x1="0" y1="0" x2="226" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line></g><g id="SvgjsG1099" class="apexcharts-annotations"></g></svg><div class="apexcharts-legend"></div></div>
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<p class="fs-28 text-black font-w600 mb-1">10 min</p>
									<span>Ticket disponible</span>
								</div>
								<div class="d-inline-block ml-auto position-relative donut-chart-sale">
										<svg class="peity" height="70" width="70"><path d="M 35 0 A 35 35 0 1 1 10.25126265847084 59.748737341529164 L 16.615223689149765 53.38477631085024 A 26 26 0 1 0 35 9" data-value="5" fill="rgb(254, 99, 78)"></path><path d="M 10.25126265847084 59.748737341529164 A 35 35 0 0 1 34.99999999999999 0 L 34.99999999999999 9 A 26 26 0 0 0 16.615223689149765 53.38477631085024" data-value="3" fill="rgba(244, 244, 244, 1)"></path></svg>
									<small class="text-primary">65%</small>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-12 col-sm-6">
					<div class="row">
						<div class="col-xl-12">
							<div class="card">
								<div class="card-body">
									<div class="d-flex align-items-end">
										<div>
											<p class="fs-14 mb-1">Nuevas ventas</p>
											<span class="fs-35 text-black font-w600">93
												<svg class="ml-1" width="19" height="12" viewBox="0 0 19 12" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z" fill="#33C25B"/>
												</svg>
											</span>
										</div>
										<canvas class="lineChart" id="chart_widget_2" height="85"></canvas>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-xl-12">
							<div class="card">
								<div class="card-header align-items-start pb-0 border-0">	
									<div>
										<h4 class="fs-16 mb-0 text-black font-w600">Crecimiento 25%</h4>
										<span class="fs-12">Comparación</span>
									</div>
									<select class="form-control style-1 default-select ">
										<option>Diaria</option>
										<option>Mensual</option>
										<option>Semanal</option>
									</select>
								</div>
								<div class="card-body pt-0">
									<div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
									<canvas id="widgetChart1" height="50" width="323" style="display: block; width: 323px; height: 50px;" class="chartjs-render-monitor"></canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
