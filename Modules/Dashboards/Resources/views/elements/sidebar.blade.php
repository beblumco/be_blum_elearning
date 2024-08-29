<!--
    |--------------------------------------------------------------------------
    | MENÚ LATERAL SIN EL LOGO PRINCIPAL HASTA EL FOOTER (COLOSENSES 3:23)
    |--------------------------------------------------------------------------
    | `Y todo lo que hagáis, hacedlo de corazón, como para el Señor, y no para los hombres`
    |
    | Acá se contiene el menú y submenús si son necesarios junto
    |
-->

<!--**********************************
 MENÚ START 
***********************************-->
<div class="deznav">
  <div class="deznav-scroll">

    <ul class="metismenu" id="menu">
      {{-- MULTIPLE MENÚ --}}
      <li>
        <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
          <i class="flaticon-381-networking"></i>
          <span class="nav-text">Dashboard</span>
        </a>
        <ul aria-expanded="false">
          <li class="{{ (request()->is('dashboard')) ? 'mm-active' : '' }}"><a class="{{ (request()->is('dashboard')) ? 'mm-active' : '' }}" href="/dashboard">Panel de Información</a></li>
        </ul>
      </li>
      {{--END - MULTIPLE MENÚ --}}
      
      {{-- SINGLE MENÚ --}}
      <li>
        <a href="{!! url('/widget-basic'); !!}" class="ai-icon" aria-expanded="false">
          <i class="flaticon-381-settings-2"></i>
          <span class="nav-text">Widget</span>
        </a>
      </li>
      {{-- END - SINGLE MENÚ --}}
    </ul>

    {{-- FOOTER MENÚ --}}
    <div class="copyright">
      <p><strong>Klaxen S.A.S</strong> © {{ date('Y') }} All Rights Reserved</p>
    </div>
    {{-- END - FOOTER MENÚ --}}

  </div>
</div>
<!--**********************************
  MENÚ END
***********************************-->
