<!--**********************************
  Sidebar start
***********************************-->
@php
    $subConsulta = \DB::table('grupo_modulo as g2')
        ->select('g2.submodulo_id')
        ->where('g2.grupo_id', auth()->user()->id_grupo);

    $respuestaMenu = \DB::table('grupo_modulo as g')
        ->join('modulos as m', 'g.modulo_id', '=', 'm.id')
        ->join('submodulos as s', 'g.submodulo_id', '=', 's.id')
        ->where('g.grupo_id', auth()->user()->id_grupo)
        ->whereIn('s.id', $subConsulta)
        ->select('m.id as idmenu', 'm.nombre as modulo', 's.nombre as submodulo', 's.nivel', 'g.submodulo_id', 'm.icono', 's.ruta', 'm.orden', 's.orden as ordenSub', 'm.ruta as rutaM', 's.submodulo_id as submodulo2')
        ->orderby('m.orden')
        ->orderby('ordenSub');
    if(auth()->user()->id != 4301)
        $respuestaMenu = $respuestaMenu->where('m.id','!=', 21);

    if(auth()->user()->main_account_id == 2){ // WEST
        $respuestaMenu = $respuestaMenu->whereIn('m.id', [1, 2, 6, 20]);
        $respuestaMenu = $respuestaMenu->whereNotIn('s.id', [1,16,27, 28]);
    }
    $respuestaMenu = $respuestaMenu->get();

    $array = [];

    foreach ($respuestaMenu as $key => $value) {
        if ($value->submodulo2 != $value->submodulo_id) {
            //$array[$value->idmenu][$value->submodulo2]['sub-submodulo'] = ['submodulo' => $value->submodulo, 'ruta' => $value->ruta];
            $array[$value->idmenu][$value->submodulo2]['sub-submodulo'][$value->submodulo_id]['submodulo'] = $value->submodulo;
            $array[$value->idmenu][$value->submodulo2]['sub-submodulo'][$value->submodulo_id]['ruta'] = $value->ruta;
        } else {
            $array[$value->idmenu]['modulo'] = $value->modulo;
            $array[$value->idmenu]['icono'] = $value->icono;
            $array[$value->idmenu]['rutaM'] = $value->rutaM;
            $array[$value->idmenu][$value->submodulo_id]['submodulo'] = $value->submodulo;
            $array[$value->idmenu][$value->submodulo_id]['nivel'] = $value->nivel;
            $array[$value->idmenu][$value->submodulo_id]['ruta'] = $value->ruta;
        }
    }

    // dd($array);
    //dd(Route::current()->getName());

@endphp


<div class="deznav d-none d-sm-block">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">

            {{--  RECORREMOS ARREGLO CREADO APARTIR DE LA CONSULTA DE MODULOS  --}}
            @foreach ($array as $key => $value)
                {{-- INIT MENÚ --}}
                <li class="contenedor_menu {{ strpos(url()->current(), $value['rutaM']) !== false ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon d-flex justify-content-center" href="javascript:void()"
                        aria-expanded="false">
                        <span class="dev_container_menu">
                            <i class="dev_icons_menu {{ $value['icono'] }}" style="font-size: 24px !important"></i>
                            <span class="w-100">{{ $value['modulo'] }}</span>
                        </span>
                        <span class="nav-text">{{ $value['modulo'] }}</span>
                    </a>
                    <ul aria-expanded="false">
                        @foreach ($value as $inner_key => $inner_value)
                            @if (is_array($inner_value))
                                {{--  @foreach ($inner_value as $ikey => $ivalue)  --}}
                                <li><a class="{{ Route::current()->getName() == $inner_value['ruta'] ? 'mm-active' : '' }}"
                                        style="white-space: nowrap;"
                                        href="{{ route($inner_value['ruta']) }}">{{ $inner_value['submodulo'] }}</a>
                                    {{--  @isset($inner_value['sub-submodulo'])
                                                    @foreach ($inner_value as $inner_keys => $subSubMenu)
                                                        @if (is_array($subSubMenu))
                                                            @foreach ($subSubMenu as $itemSub)
                                                                <ul aria-expanded="false" style="left: 100% !important;">
                                                                    <li><a class="{{ Route::current()->getName() == $itemSub['ruta'] ? 'mm-active' : '' }}"
                                                                            style="white-space: nowrap;"
                                                                            href="{{ route($itemSub['ruta']) }}">{{ $itemSub['submodulo'] }}</a>

                                                                    </li>
                                                                </ul>
                                                            @endforeach

                                                        @endif
                                                    @endforeach
                                                @endisset  --}}
                                </li>
                                {{--  @endforeach  --}}
                            @endif
                        @endforeach
                    </ul>
                </li>
                {{-- END MENÚ --}}
            @endforeach

        </ul>

        <div class="copyright">
            <p><strong>Klaxen</strong> © {{ date('Y') }} Todos los derechos reservados</p>
            {{-- <p>Made with <span class="heart"></span> by DexignZone</p> --}}
        </div>

    </div>
</div>

<!--**********************************
  Sidebar end
***********************************-->
