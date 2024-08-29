		 <div class="deznav">
			 <div class="deznav-scroll">
				 <!-- <a href="javascript:void(0)" class="add-menu-sidebar" data-toggle="modal" data-target="#addOrderModalside" >+ New Event</a> -->
				 <ul class="metismenu" id="menu">
					 <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							 <i class="flaticon-381-networking"></i>
							 <span class="nav-text">Panel de información</span>
						 </a>
						 <ul aria-expanded="false">
							 <li class="{{ (request()->is('dashboard')) ? 'mm-active' : '' }}"><a class="{{ (request()->is('dashboard')) ? 'mm-active' : '' }}" href="/dashboard">Panel de información</a></li>
						 </ul>
					 </li>
					 <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							 <i class="flaticon-381-home-1"></i>
							 <span class="nav-text">Mi organización</span>
						 </a>
						 <ul aria-expanded="false">
							 <li><a href="./app-profile.html">Mi organización</a></li>
						 </ul>
					 </li>
					 <li class="{{ (request()->is('drive')) ? 'mm-active' : '' }}"><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							 <i class="flaticon-381-cloud"></i>
							 <span class="nav-text">Drive</span>
						 </a>
						 <ul aria-expanded="false">
							 <li><a class="{{ (request()->is('drive')) ? 'mm-active' : '' }}" href="/drive">Drive</a></li>
						 </ul>
					 </li>
					 <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							 <i class="flaticon-381-list-1"></i>
							 <span class="nav-text">Reportes</span>
						 </a>
						 <ul aria-expanded="false">
							 <li><a href="./ui-accordion.html">Reportes</a></li>

						 </ul>
					 </li>
				 </ul>
				 <div class="copyright">
					 <p><strong>Klaxen</strong> © 2021 All Rights Reserved</p>
					 <p>Made with <span class="heart"></span> by DexignZone</p>
				 </div>
			 </div>
		 </div>

