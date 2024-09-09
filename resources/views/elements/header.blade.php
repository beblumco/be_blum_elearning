@php
    if(\Auth::user()->savk_principal == 1){
        $centros_costo = \DB::table('punto_evaluacion')
        ->select('nombre as name', 'id')
        ->where([['estado', 1], ['main_account_id', \Auth::user()->main_account_id]])
        ->get();
    }else{
        //SE BUSCA A QUE GRUPO EMPRESA PERTENECE PARA POSTERIORMENTE CONSULTAR TODOS LOS PUNTOS QUE PERTENECEN A ESTE
        $gEmpresa = \DB::table('unidad as u')
        ->select('u.centro_operacion_id')
        ->join('punto_evaluacion as p', 'p.unidad_id', 'u.id')
        ->where('p.id', \Auth::user()->id_punto)
        ->first();

        if($gEmpresa){
            $centros_costo = \DB::table('punto_evaluacion as p')
            ->select('p.nombre as name', 'p.id')
            ->join('unidad as u', 'p.unidad_id', 'u.id')
            ->where([['p.estado', 1], ['u.centro_operacion_id', $gEmpresa->centro_operacion_id]])
            ->get();
        }else{
            $centros_costo = [];
        }
    }

@endphp

<!--**********************************
  Chat box start
***********************************-->
<!-- Latest compiled and minified CSS -->
<link defer rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>
<div class="chatbox">
    <div class="chatbox-close"></div>
    <div class="custom-tab-1">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#notes">Notes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#alerts">Alerts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#chat">Chat</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active show" id="chat" role="tabpanel">
                <div class="card mb-sm-3 mb-md-0 contacts_card dz-chat-user-box">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect fill="#000000" x="4" y="11" width="16" height="2"
                                        rx="1" />
                                    <rect fill="#000000" opacity="0.3"
                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                        x="4" y="11" width="16" height="2" rx="1" />
                                </g>
                            </svg></a>
                        <div>
                            <h6 class="mb-1">Chat List</h6>
                            <p class="mb-0">Show All</p>
                        </div>
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                </g>
                            </svg></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll  " id="DZ_W_Contacts_Body">
                        <ul class="contacts">
                            <li class="name-first-letter">A</li>
                            <li class="active dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Archie Parker</span>
                                        <p>Kalid is online</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Alfie Mason</span>
                                        <p>Taherah left 7 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/3.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>AharlieKane</span>
                                        <p>Sami is online</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/4.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Athan Jacoby</span>
                                        <p>Nargis left 30 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="name-first-letter">B</li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/5.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Bashid Samim</span>
                                        <p>Rashid left 50 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Breddie Ronan</span>
                                        <p>Kalid is online</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Ceorge Carson</span>
                                        <p>Taherah left 7 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="name-first-letter">D</li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/3.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Darry Parker</span>
                                        <p>Sami is online</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/4.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Denry Hunter</span>
                                        <p>Nargis left 30 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="name-first-letter">J</li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/5.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Jack Ronan</span>
                                        <p>Rashid left 50 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Jacob Tucker</span>
                                        <p>Kalid is online</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>James Logan</span>
                                        <p>Taherah left 7 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/3.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Joshua Weston</span>
                                        <p>Sami is online</p>
                                    </div>
                                </div>
                            </li>
                            <li class="name-first-letter">O</li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/4.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Oliver Acker</span>
                                        <p>Nargis left 30 mins ago</p>
                                    </div>
                                </div>
                            </li>
                            <li class="dz-chat-user">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont">
                                        <img src="{{ asset('assets/images/avatar/5.jpg') }}"
                                            class="rounded-circle user_img" alt="" />
                                        <span class="online_icon offline"></span>
                                    </div>
                                    <div class="user_info">
                                        <span>Oscar Weston</span>
                                        <p>Rashid left 50 mins ago</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card chat dz-chat-history-box d-none">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)" class="dz-chat-history-back">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="18px" height="18px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <rect fill="#000000" opacity="0.3"
                                        transform="translate(15.000000, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-15.000000, -12.000000) "
                                        x="14" y="7" width="2" height="10"
                                        rx="1" />
                                    <path
                                        d="M3.7071045,15.7071045 C3.3165802,16.0976288 2.68341522,16.0976288 2.29289093,15.7071045 C1.90236664,15.3165802 1.90236664,14.6834152 2.29289093,14.2928909 L8.29289093,8.29289093 C8.67146987,7.914312 9.28105631,7.90106637 9.67572234,8.26284357 L15.6757223,13.7628436 C16.0828413,14.136036 16.1103443,14.7686034 15.7371519,15.1757223 C15.3639594,15.5828413 14.7313921,15.6103443 14.3242731,15.2371519 L9.03007346,10.3841355 L3.7071045,15.7071045 Z"
                                        fill="#000000" fill-rule="nonzero"
                                        transform="translate(9.000001, 11.999997) scale(-1, -1) rotate(90.000000) translate(-9.000001, -11.999997) " />
                                </g>
                            </svg>
                        </a>
                        <div>
                            <h6 class="mb-1">Chat with Khelesh</h6>
                            <p class="mb-0 text-success">Online</p>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0)" data-toggle="dropdown"><svg
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="18px" height="18px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <circle fill="#000000" cx="5" cy="12" r="2" />
                                        <circle fill="#000000" cx="12" cy="12" r="2" />
                                        <circle fill="#000000" cx="19" cy="12" r="2" />
                                    </g>
                                </svg></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-item"><i class="fa fa-user-circle text-primary mr-2"></i> View
                                    profile</li>
                                <li class="dropdown-item"><i class="fa fa-users text-primary mr-2"></i> Add to close
                                    friends</li>
                                <li class="dropdown-item"><i class="fa fa-plus text-primary mr-2"></i> Add to group
                                </li>
                                <li class="dropdown-item"><i class="fa fa-ban text-primary mr-2"></i> Block</li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body msg_card_body dz-scroll" id="DZ_W_Contacts_Body3">
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                Hi, how are you samim?
                                <span class="msg_time">8:40 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="msg_cotainer_send">
                                Hi Khalid i am good tnx how about you?
                                <span class="msg_time_send">8:55 AM, Today</span>
                            </div>
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                I am good too, thank you for your chat template
                                <span class="msg_time">9:00 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="msg_cotainer_send">
                                You are welcome
                                <span class="msg_time_send">9:05 AM, Today</span>
                            </div>
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                I am looking for your next templates
                                <span class="msg_time">9:07 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="msg_cotainer_send">
                                Ok, thank you have a good day
                                <span class="msg_time_send">9:10 AM, Today</span>
                            </div>
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                Bye, see you
                                <span class="msg_time">9:12 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                Hi, how are you samim?
                                <span class="msg_time">8:40 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="msg_cotainer_send">
                                Hi Khalid i am good tnx how about you?
                                <span class="msg_time_send">8:55 AM, Today</span>
                            </div>
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                I am good too, thank you for your chat template
                                <span class="msg_time">9:00 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="msg_cotainer_send">
                                You are welcome
                                <span class="msg_time_send">9:05 AM, Today</span>
                            </div>
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                I am looking for your next templates
                                <span class="msg_time">9:07 AM, Today</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mb-4">
                            <div class="msg_cotainer_send">
                                Ok, thank you have a good day
                                <span class="msg_time_send">9:10 AM, Today</span>
                            </div>
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/2.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-start mb-4">
                            <div class="img_cont_msg">
                                <img src="{{ asset('assets/images/avatar/1.jpg') }}"
                                    class="rounded-circle user_img_msg" alt="" />
                            </div>
                            <div class="msg_cotainer">
                                Bye, see you
                                <span class="msg_time">9:12 AM, Today</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer type_msg">
                        <div class="input-group">
                            <textarea class="form-control" placeholder="Type your message..."></textarea>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary"><i
                                        class="fa fa-location-arrow"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="alerts" role="tabpanel">
                <div class="card mb-sm-3 mb-md-0 contacts_card">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                </g>
                            </svg></a>
                        <div>
                            <h6 class="mb-1">Notications</h6>
                            <p class="mb-0">Show All</p>
                        </div>
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </g>
                            </svg></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body1">
                        <ul class="contacts">
                            <li class="name-first-letter">SEVER STATUS</li>
                            <li class="active">
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont primary">KK</div>
                                    <div class="user_info">
                                        <span>David Nester Birthday</span>
                                        <p class="text-primary">Today</p>
                                    </div>
                                </div>
                            </li>
                            <li class="name-first-letter">SOCIAL</li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont success">RU<i class="icon fa-birthday-cake"></i></div>
                                    <div class="user_info">
                                        <span>Perfection Simplified</span>
                                        <p>Jame Smith commented on your status</p>
                                    </div>
                                </div>
                            </li>
                            <li class="name-first-letter">SEVER STATUS</li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont primary">AU<i class="icon fa fa-user-plus"></i></div>
                                    <div class="user_info">
                                        <span>AharlieKane</span>
                                        <p>Sami is online</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="img_cont info">MO<i class="icon fa fa-user-plus"></i></div>
                                    <div class="user_info">
                                        <span>Athan Jacoby</span>
                                        <p>Nargis left 30 mins ago</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="notes">
                <div class="card mb-sm-3 mb-md-0 note_card">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect fill="#000000" x="4" y="11" width="16"
                                        height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.3"
                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                        x="4" y="11" width="16" height="2"
                                        rx="1" />
                                </g>
                            </svg></a>
                        <div>
                            <h6 class="mb-1">Notes</h6>
                            <p class="mb-0">Add New Nots</p>
                        </div>
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path
                                        d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                        fill="#000000" fill-rule="nonzero" />
                                </g>
                            </svg></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body2">
                        <ul class="contacts">
                            <li class="active">
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>New order placed..</span>
                                        <p>10 Aug 2020</p>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-xs sharp mr-1"><i
                                                class="fa fa-pencil"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs sharp"><i
                                                class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>Youtube, a video-sharing website..</span>
                                        <p>10 Aug 2020</p>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-xs sharp mr-1"><i
                                                class="fa fa-pencil"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs sharp"><i
                                                class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>john just buy your product..</span>
                                        <p>10 Aug 2020</p>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-xs sharp mr-1"><i
                                                class="fa fa-pencil"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs sharp"><i
                                                class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex bd-highlight">
                                    <div class="user_info">
                                        <span>Athan Jacoby</span>
                                        <p>10 Aug 2020</p>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-xs sharp mr-1"><i
                                                class="fa fa-pencil"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs sharp"><i
                                                class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--**********************************
  Chat box End
***********************************-->

<!--**********************************
  Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('title', $page_title ?? 'Dashboard')
                    </div>
                </div>
                <ul class="navbar-nav header-right">

                    {{-- <li class="nav-item dropdown notification_dropdown">
						<a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-toggle="dropdown" style="padding: 17px;">
							<i class="flaticon-381-pad"></i>
						</a>
						<div class="dropdown-menu rounded dropdown-menu-right">
							<div id="DZ_W_Notification1" class="widget-media dz-scroll p-3">
								<ul class="timeline">
									<li style="cursor: pointer;" onclick="OnClickAudeed();">
										<div class="timeline-panel">
											<div class="media mr-2">
												<img alt="image" width="50" src="{{ asset('img/1.jpg') }}">
											</div>
											<div class="media-body">
												<h6 class="mb-1">Audeed</h6>
												<small class="d-block">Auditorías en línea</small>
											</div>
										</div>
									</li>

								</ul>
							</div>
					</li> --}}

                    {{-- <li class="nav-item dropdown notification_dropdown">
						<a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-toggle="dropdown">
							<svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M12.8333 5.91732V3.49998C12.8333 2.85598 13.356 2.33331 14 2.33331C14.6428 2.33331 15.1667 2.85598 15.1667 3.49998V5.91732C16.9003 6.16698 18.5208 6.97198 19.7738 8.22498C21.3057 9.75681 22.1667 11.8346 22.1667 14V18.3913L23.1105 20.279C23.562 21.1831 23.5142 22.2565 22.9822 23.1163C22.4513 23.9761 21.5122 24.5 20.5018 24.5H15.1667C15.1667 25.144 14.6428 25.6666 14 25.6666C13.356 25.6666 12.8333 25.144 12.8333 24.5H7.49817C6.48667 24.5 5.54752 23.9761 5.01669 23.1163C4.48469 22.2565 4.43684 21.1831 4.88951 20.279L5.83333 18.3913V14C5.83333 11.8346 6.69319 9.75681 8.22502 8.22498C9.47919 6.97198 11.0985 6.16698 12.8333 5.91732ZM14 8.16664C12.4518 8.16664 10.969 8.78148 9.87469 9.87581C8.78035 10.969 8.16666 12.453 8.16666 14V18.6666C8.16666 18.8475 8.12351 19.026 8.04301 19.1881C8.04301 19.1881 7.52384 20.2265 6.9755 21.322C6.88567 21.5028 6.89501 21.7186 7.00117 21.8901C7.10734 22.0616 7.29517 22.1666 7.49817 22.1666H20.5018C20.7037 22.1666 20.8915 22.0616 20.9977 21.8901C21.1038 21.7186 21.1132 21.5028 21.0234 21.322C20.475 20.2265 19.9558 19.1881 19.9558 19.1881C19.8753 19.026 19.8333 18.8475 19.8333 18.6666V14C19.8333 12.453 19.2185 10.969 18.1242 9.87581C17.0298 8.78148 15.547 8.16664 14 8.16664Z" fill="#FE634E"/>
							</svg>
							<div class="pulse-css"></div>
						</a>
						<div class="dropdown-menu rounded dropdown-menu-right">
							<div id="DZ_W_Notification1" class="widget-media dz-scroll p-3 height229" >
								<ul class="timeline">

                  <li>
										<div class="timeline-panel">
											<div class="media mr-2 media-info">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
											</div>
											<div class="media-body">
												<h6 class="mb-1">Acompañamiento</h6>
												<small class="d-block">POSTOBÓN - PLANTA YUMBO - 21 Jun 2022 - 02:11 PM</small>
											</div>
										</div>
									</li>
									<li>
										<div class="timeline-panel">
											<div class="media mr-2 media-info">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
											</div>
											<div class="media-body">
												<h6 class="mb-1">Acompañamiento</h6>
												<small class="d-block">POSTOBÓN - PLANTA MEDELLÍN - 14 Jun 2022 - 01:00 PM</small>
											</div>
										</div>
									</li>

								</ul>
							</div>
							<a class="all-notification" href="javascript:void(0)">Ver todas <i class="ti-arrow-right"></i></a>
						</div>
					</li> --}}

          <div class="d-flex justify-content-center align-items-center">
            <button class="btn btn-barrat"  data-toggle="modal" data-target="#puntosModal"><span id="puntosAcumulados"></span> Puntos</button>
          </div>

                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0)" role="button" data-toggle="dropdown">
                            <img id="imgAvatar"
                                src="{{ $dataUser->img_avatar == null ? asset('img/' . $dataUser->IMG_AVATAR) : env('API') . 'storage/' . $dataUser->IMG_AVATAR }}"
                                width="20" alt="" />
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <div>
                                <p class="ml-4 text-color-savk font-weight-bold">¡Hola, <span class="nombreUSer"></span>!</p>
                            </div>
                            <a href="#" onclick="openModalMiPerfil()" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-savk"
                                    width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ml-2">Perfil </span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}" id="form_logout">
                                {{ csrf_field() }}
                                <a href="javascript:;" onclick="document.getElementById('form_logout').submit();"
                                    class="dropdown-item ai-icon">
                                    <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-savk"
                                        width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span class="ml-2">Salir</span>
                                </a>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- Modal puntos -->
<div class="modal fade" id="puntosModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <p style="color: black; font-weight: 300"><span class="nombreUSer"></span>, hasta la fecha has obtenido <span id="sumaPuntos"></span> Puntos:</p>
                    </div>
                </div>
                <div class="row">
                    <div class="{{ Auth::user()->main_account_id != 2 ? 'col-lg-6' : 'd-none' }}">
                        <div class="col-lg-12"
                            style="
                            box-shadow: 0 0.46875rem 2.1875rem rgba(4,9,20,0.03), 0 0.9375rem 1.40625rem rgba(4,9,20,0.03), 0 0.25rem 0.53125rem rgba(4,9,20,0.05), 0 0.125rem 0.1875rem rgba(4,9,20,0.03) !important;
                            border-radius: 1.25rem;
                            padding: 1rem;
                        ">
                            <div class="row">
                                <div class="col-lg-12 d-flex align-items-center justify-content-center mb-2">
                                    <img src="{{ env('URL') }}img/logo_principal_primary.png"
                                        style="
                                            height: 90px;
                                    ">
                                </div>
                                <div class="col-lg-12 d-flex align-items-center justify-content-center" style="color: #002f54; font-weight: bold;">
                                    <span style="font-size: 27px; margin-right: 3px;" id="puntos-klaxen"></span> Puntos
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="{{ Auth::user()->main_account_id != 2 ? 'col-lg-6' : 'col-lg-12' }}">
                        <div class="col-lg-12"
                            style="
                            box-shadow: 0 0.46875rem 2.1875rem rgba(4,9,20,0.03), 0 0.9375rem 1.40625rem rgba(4,9,20,0.03), 0 0.25rem 0.53125rem rgba(4,9,20,0.05), 0 0.125rem 0.1875rem rgba(4,9,20,0.03) !important;
                            border-radius: 1.25rem;
                            padding: 1rem;
                        ">
                            <div class="row">
                                <div class="col-lg-12 d-flex align-items-center justify-content-center mb-2">
                                    <img id="empresaAvatar" src="{{ env('URL') }}img/logo_principal_primary.png"
                                        style="width: 160px;">
                                </div>
                                <div class="col-lg-12 d-flex align-items-center justify-content-center" style="color: #002f54; font-weight: bold;">
                                    <span style="font-size: 27px; margin-right: 3px;" id="puntos-empresa"></span> Puntos
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- MODAL CREAR USUARIO -->
<div class="modal fade" id="modal_perfil" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title-create-company">
                    Mi perfil
                </h5>
                <button type="button" class="close" onclick="closeModalMiPerfil()">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="messageError" style="display: none" class="alert alert-warning alert-dismissible fade show"
                    role="alert">
                    Los campos con (*) son obligatorios, asegurese de diligenciarlos.
                </div>
                <div id="messagePassword" style="display: none"
                    class="alert alert-warning alert-dismissible fade show" role="alert">
                    El campo cambiar contraseña y su confirmacion no coinciden.
                </div>

                <div id="messageSuccess" style="display: none"
                    class="alert alert-success alert-dismissible fade show" role="alert">
                </div>

                <div class="row m-auto">
                    <div class="form-group col-md-6">
                        <label>Avatar: <span class="dev-required"></span></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input onchange="labelFile()" id="avatar" type="file" accept="image/*" class="custom-file-input">
                                <label id="labelFile" class="custom-file-label">Seleccione una imagen...</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name" class="col-form-label">
                            Nombre completo: <span style="color: red">*</span></label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input id="nombre_com" type="text" class="form-control input-default" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name" class="col-form-label">
                            Numero de documentacion:</label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input id="codigo" type="number" class="form-control input-default" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name" class="col-form-label">
                            Correo: <span style="color: red">*</span></label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input id="email" type="email" class="form-control input-default" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="name" class="col-form-label">
                            Cambiar contraseña: <span style="color: red"></span></label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input id="password" type="password" class="form-control input-default" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name" class="col-form-label">
                            Confirmar contraseña: <span style="color: red"></span></label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input id="confirm_password" type="password" class="form-control input-default" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name" class="col-form-label">
                            Centro de costo asignado: <span style="color: red">*</span></label>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <select id="id_punto" class="selectpicker" title="Selecciona una opcion..."
                                    data-live-search="true">
                                    @foreach ($centros_costo as $centro)
                                        <option id="opcion_{{ $centro->id }}" value="{{ $centro->id }}"
                                            data-tokens="{{ $centro->name }}">
                                            {{ $centro->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="updatePerfil()">
                    Guardar
                </button>
                <button type="button" class="btn btn-danger light" onclick="closeModalMiPerfil()">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@php
    $user_id = \Auth::user()->id;
@endphp
<!--**********************************
  Header end ti-comment-alt
***********************************-->
<script>
    async function getPerfil() {
        try {
            //load(true);
            const response = await fetch(
                document.querySelector('meta[name="csrf-token"]').getAttribute("url") +
                "administration/usuarios/mi-perfil", {
                    method: "GET",
                    headers: {
                        "Content-type": "application/json; charset=UTF-8",
                        "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            "content"),
                    },
                });
            // //load(false);
            const resp = await response.json();

            switch (resp.status) {
                case 201:

                    break;

                case 202:
                    document.getElementById("codigo").value = resp.data.codigo != null ? resp.data.codigo : '';
                    document.getElementById("nombre_com").value = resp.data.nombre_com != null ? resp.data
                        .nombre_com : '';
                    document.getElementById("id_punto").value = resp.data.id_punto != null ? resp.data.id_punto :
                        '';
                    //document.getElementById("opcion_" + resp.data.id_punto).setAttribute('selected', 'selected');
                    document.getElementById("email").value = resp.data.email != null ? resp.data.email : '';
                    document.getElementById("id_punto").value = resp.data.id_punto;
                    $("#id_punto").selectpicker('render');
                    break;

                default:

                    break;
            }
        } catch (error) {
            console.log(error);
        }
    }

    function openModalMiPerfil() {
        document.getElementById('messageSuccess').style.display = 'none';
        document.getElementById('messageError').style.display = 'none';
        $('#modal_perfil').modal('show');
        getPerfil();
    }

    function closeModalMiPerfil() {
        $('#modal_perfil').modal('hide');
    }


    async function updatePerfil() {
        document.getElementById('messageSuccess').style.display = 'none';
        document.getElementById('messageError').style.display = 'none';
        document.getElementById('messagePassword').style.display = 'none';
        try {

            let response;
            let resp;
            const api = document.querySelector('meta[name="csrf-token"]').getAttribute("api")
            const id_user = @json($user_id);
            if (document.getElementById('avatar').files[0] != null) {
                let data_form = new FormData();

                data_form.append('photo', document.getElementById('avatar').files[0])
                data_form.append('idUser', id_user)
                loading(true);
                response = await fetch(`${api}api/save_profile_picture`, { method: "POST", body: data_form });
                loading(false);
                resp = await response.json();

                switch (resp.responseCode) {
                    case 201:

                        break;

                    case 200:
                        $("#imgAvatar").attr("src",
                            (document
                                .querySelector('meta[name="csrf-token"]').getAttribute("api") +
                                'storage/' + resp.data)
                        );
                        break;

                    default:

                        break;
                }

            }

            let form = {
                id: {
                    required: false,
                    val: {{ Auth::user()->id }}
                },
                nombre: {
                    required: true,
                    val: document.getElementById('nombre_com').value
                },
                codigo: {
                    required: false,
                    val: document.getElementById('codigo').value
                },
                email: {
                    required: true,
                    val: document.getElementById('email').value
                },
                punto: {
                    required: true,
                    val: document.getElementById('id_punto').value
                },
                password: {
                    required: false,
                    val: document.getElementById('password').value
                },
                profile: {
                    required: false,
                    val: ""
                },
                tel: {
                    required: false,
                    val: ""
                },
                lider_empresas: {
                    required: false,
                    val: []
                },
                estado: {
                    required: true,
                    val: 1
                },
                pais: {
                    required: false,
                    val: ""
                },
                departamento: {
                    required: false,
                    val: ""
                },
                ciudad: {
                    required: false,
                    val: ""
                }
            }


            if (validateForm(form)) {
                form.mode = 'edit';
                loading(true);
                    response = await fetch(
                        document.querySelector('meta[name="csrf-token"]').getAttribute("url") +
                        "administration/usuarios/crear", {
                            method: "POST",
                            headers: {
                                "Content-type": "application/json; charset=UTF-8",
                                "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    "content"),
                            },
                            body: JSON.stringify(form),
                        });
                loading(false);
                resp = await response.json();

                switch (resp.status) {
                    case 201:
                        document.getElementById('messageSuccess').style.display = 'block';
                        document.getElementById('messageSuccess').innerHTML = resp.msg
                        getPerfil();
                        break;

                    case 202:

                        break;

                    default:

                        break;
                }
            }


        } catch (error) {
            console.log(error);
        }
    }

    function validateForm(data) {
        let next = true;

        Object.keys(data).forEach((el) => {
            if ((data[el].val === "" || data[el].val === undefined) && data[
                    el].required) {
                next = false;
                document.getElementById('messageError').style.display = 'block';
            }

            if (el == 'password') {
                if (data[el].val.length > 0 && data[el].val != document.getElementById('confirm_password')
                    .value) {
                    document.getElementById('messagePassword').style.display = 'block';
                    next = false;
                }
            }
        });

        return next;
    }
    function labelFile(){
        if(document.getElementById('avatar').files[0] != null){
          const maxSize = 3 * 1024 * 1024; // 3MB en bytes

            if (document.getElementById('avatar').files[0].size <= maxSize) {
                document.getElementById('labelFile').innerHTML = "Un archivo seleccionado."
            }else{
                swal({
                    title: "Error de imagen",
                    text: `El tamaño de la imagen excede el límite de 3 MB.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "No",
                    confirmButtonColor: '#1f3352',
                    allowOutsideClick: false
                });
                document.getElementById('avatar').value = "";
            }
        }
    }
</script>
