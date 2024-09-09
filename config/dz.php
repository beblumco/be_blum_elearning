<?php
$time = time();
return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'BeBlum'),


    'public' => [
        'favicon' => 'media/img/logo/favicon.ico',
        'fonts' => [
            'google' => [
                'families' => [
                    'Poppins:300,400,500,600,700'
                ]
            ]
        ],
        'global' => [
            'css' => [
                'https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap',
                'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css',
                'assets/vendor/select2/css/select2.min.css',
                'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                'assets/vendor/toastr/css/toastr.min.css',
                'assets/css/main.css?v='.$time,
                'assets/css/style.css'
            ],
            'js' => [
                'assets/vendor/global/global.min.js',
                'assets/js/main.js?v=' . $time,
                'assets/js/custom.min.js'
            ],
        ],
        'pagelevel' => [
            'css' => [
                // 'IndexMyOrganization' => [
                //     'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                //     'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                //     'assets/vendor/toastr/css/toastr.min.css',
                //     'assets/administration/virtual_training/main.css?v=' . $time
                // ],
                'DashboardPrincipalIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/dashboard_module/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'DashboardCorporativoIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/dashboard_module/main_corporativo.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'IndicadoresEquipo' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/css/main.css?v=' . $time
                ],
                'WelcomeIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/welcome_module/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'DriveIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/lightgallery/css/lightgallery.min.css',
                    'assets/drive_module/main.css?v=' . $time
                ],
                'IndexBBC' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/bbc_module/main.css?v=' . $time
                ],
                'IndexDownloadBBC' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/bbc_module/download_certificate/main.css?v=' . $time
                ],
                'IndexDownloadMatrizInsumoBBC' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/bbc_module/download_matriz_insumo/main.css?v=' . $time
                ],
                'IndexFormBBC' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/bbc_module/form/main.css?v=' . $time
                ],
                'RecordIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/record_module/main.css?v=' . $time
                ],
                'PropuestaDetalleView' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/dashboard_module/detail_dashboard/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'IndexReports' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/chartist/css/chartist.min.css',
                    'assets/reports_module/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'TrainingsIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'DriveIndexV' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/lightgallery/css/lightgallery.min.css',
                    'assets/drive_module/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'DriveIndexEntornoAprendizaje' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/lightgallery/css/lightgallery.min.css',
                    'assets/drive_module/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'TrainingsIndexMenu' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'TrainingsIndexTraining' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'TrainingsAssistedByExpertIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css',
                    'https://fonts.googleapis.com/icon?family=Material+Icons',
                ],
                'TrainingsIndexCertificates' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/certificates_module/main.css?v=' . $time
                ],
                'GetTrainingShare' => [
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'shop_module' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/owl-carousel/owl.carousel.css',
                    'assets/shop_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'ia_module' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/owl-carousel/owl.carousel.css',
                    'assets/ia_module/main.css?v=' . $time,
                    'assets/ia_module/tw.css',
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'shopping_car' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/lightgallery/css/lightgallery.min.css',
                    'assets/shopping_car/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'IndexHistorical' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/historical/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'IndexReports' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/reports/main.css?v=' . $time,
                    'assets/css/main.css?v=' . $time
                ],
                'IndexAdminTrainings' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/administration/virtual_training/main.css?v=' . $time
                ],
                'TrainingsAdminIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_admin_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'TrainingsAdminIndexNew' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/select2/css/select2.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',

                    // 'assets/vendor/bootstrap-daterangepicker/daterangepicker.css',
                    // 'assets/vendor/clockpicker/css/bootstrap-clockpicker.min.css',
                    // 'assets/vendor/jquery-asColorPicker/css/asColorPicker.min.css',
                    'assets/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css',
                    // 'assets/vendor/pickadate/themes/default.css',
                    // 'assets/vendor/pickadate/themes/default.date.css',
                    'https://fonts.googleapis.com/icon?family=Material+Icons',
                ],
                'WebinarsIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'LoginView' => [
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'RecoverPasswordView' => [
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'NewPasswordView' => [
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'RegisterView' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/select2/css/select2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'RegisterLiderGrupoEmpresaView' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/select2/css/select2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'RegisterColaboradorView' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/select2/css/select2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'RegisterAsistView' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/select2/css/select2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'RegisterAsistAsistidaView' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/select2/css/select2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css',
                    'assets/css/auth/login/main.css?v=' . $time
                ],
                'IndexMyOrganization' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_admin_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'IndexReportAccompaniment' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_admin_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'IndexAccompaniment' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_admin_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'IndexReportTraining' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_admin_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'IndexReportTrainingAsistidas' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'detalleAuditoria' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_admin_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'MyClientsIndex' => [
                    'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.css',
                    'assets/training_admin_module/main.css?v=' . $time,
                    'assets/vendor/toastr/css/toastr.min.css'
                ],
                'event' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/datatables/css/jquery.dataTables.min.css',
                ],
                'Register' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/select2/css/select2.min.css',
                ],
                'event_detail' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/chartist/css/chartist.min.css',
                ],
                'customers' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/datatables/css/jquery.dataTables.min.css',
                ],
                'analytics' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/chartist/css/chartist.min.css',
                ],
                'reviews' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/datatables/css/jquery.dataTables.min.css',
                ],
                'app_calender' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/fullcalendar/css/fullcalendar.min.css',
                ],
                'app_profile' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/lightgallery/css/lightgallery.min.css',
                ],
                'post_details' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/lightgallery/css/lightgallery.min.css',
                ],
                'chart_chartist' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/chartist/css/chartist.min.css',
                ],
                'chart_chartjs' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'chart_flot' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',

                ],
                'chart_morris' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'chart_peity' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'chart_sparkline' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ecom_checkout' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ecom_customers' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ecom_invoice' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ecom_product_detail' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/star-rating/star-rating-svg.css',
                ],
                'ecom_product_grid' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ecom_product_list' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/star-rating/star-rating-svg.css',
                ],
                'ecom_product_order' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'email_compose' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/dropzone/dist/dropzone.css',
                ],
                'email_inbox' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'email_read' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'form_editor_summernote' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/summernote/summernote.css',
                ],
                'form_element' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'form_pickers' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/bootstrap-daterangepicker/daterangepicker.css',
                    'vendor/clockpicker/css/bootstrap-clockpicker.min.css',
                    'vendor/jquery-asColorPicker/css/asColorPicker.min.css',
                    'vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css',
                    'vendor/pickadate/themes/default.css',
                    'vendor/pickadate/themes/default.date.css',
                    'https://fonts.googleapis.com/icon?family=Material+Icons',
                ],
                'form_validation_jquery' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'form_wizard' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/jquery-smartwizard/dist/css/smart_wizard.min.css',
                ],
                'map_jqvmap' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/jqvmap/css/jqvmap.min.css',
                ],
                'table_bootstrap_basic' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'table_datatable_basic' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/datatables/css/jquery.dataTables.min.css',
                ],
                'uc_lightgallery' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/lightgallery/css/lightgallery.min.css',
                ],
                'uc_nestable' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/nestable2/css/jquery.nestable.min.css',
                ],
                'uc_noui_slider' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/nouislider/nouislider.min.css',
                ],
                'uc_select2' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/select2/css/select2.min.css',
                ],
                'uc_sweetalert' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
'vendor/sweetalert2/dist/sweetalert2.min.css',
                ],
                'uc_toastr' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/toastr/css/toastr.min.css',
                ],
                'ui_accordion' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_alert' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_badge' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_button' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_button_group' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_card' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_carousel' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_dropdown' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_grid' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_list_group' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_media_object' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_modal' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_pagination' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_popover' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_progressbar' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_tab' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'ui_typography' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                ],
                'widget_basic' => [
                    'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                    'vendor/chartist/css/chartist.min.css',
                ],
            ],
            'js' => [
                // 'IndexMyOrganization' => [
                //     'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                //     'assets/js/custom.js',
                //     'assets/vendor/select2/js/select2.full.min.js',
                //     'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                //     'assets/js/plugins-init/select2-init.js',
                //     'assets/administration/my_organization/main.js?v=' . $time,
                //     'assets/js/deznav-init.js'
                // ],
                'DashboardPrincipalIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/moment/moment.min.js',
                    'assets/dashboard_module/init.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'DashboardCorporativoIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/moment/moment.min.js',
                    'assets/dashboard_module/init.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'IndicadoresEquipo' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/moment/moment.min.js',
                    'assets/dashboard_module/init.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'WelcomeIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'PropuestaDetalleView' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/dashboard_module/detail_dashboard/init.js?v=' . $time,
                    'assets/js/deznav-init.js',
                ],
                'IndexReports' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/chart.js/Chart.bundle.min.js',
                    'assets/vendor/peity/jquery.peity.min.js',
                    'assets/vendor/apexchart/apexchart.js',
                    'assets/js/dashboard/analytics.js',
                    'assets/js/custom.js',
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/reports_module/init.js?v=' . $time,
                    'assets/js/deznav-init.js',
                ],
                'DriveIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/lightgallery/js/lightgallery-all.min.js',
                    'assets/drive_module/init.js?v=' . $time,
                    'assets/drive_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js'
                ],
                'DriveIndexV' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/lightgallery/js/lightgallery-all.min.js',
                    'assets/js/deznav-init.js',
                    'assets/vendor/moment/moment.min.js',
                    mix('/js/app.js'),
                ],
                'DriveIndexEntornoAprendizaje' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/lightgallery/js/lightgallery-all.min.js',
                    'assets/js/deznav-init.js',
                    'assets/vendor/moment/moment.min.js',
                    mix('/js/app.js'),
                ],
                'IndexBBC' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/bbc_module/init.js?v=' . $time,
                    'assets/bbc_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js'
                ],
                'IndexDownloadBBC' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/bbc_module/download_certificate/init.js?v=' . $time,
                    'assets/bbc_module/download_certificate/main.js?v=' . $time,
                    'assets/js/deznav-init.js'
                ],
                'IndexFormBBC' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/bbc_module/form/init.js?v=' . $time,
                    'assets/bbc_module/form/main.js?v=' . $time,
                    'assets/js/deznav-init.js'
                ],
                'IndexDownloadMatrizInsumoBBC' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/bbc_module/download_matriz_insumo/init.js?v=' . $time,
                    'assets/bbc_module/download_matriz_insumo/main.js?v=' . $time,
                    'assets/js/deznav-init.js'
                ],
                'RecordIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/record_module/init.js?v=' . $time,
                    'assets/record_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js'
                ],
                'TrainingsIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_module/init.js?v=' . $time,
                    'assets/training_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'TrainingsIndexMenu' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_module/init.js?v=' . $time,
                    'assets/training_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'TrainingsIndexTraining' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_module/init.js?v=' . $time,
                    'assets/training_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'TrainingsAssistedByExpertIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_module/init.js?v=' . $time,
                    'assets/training_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),

                    'assets/vendor/moment/moment.min.js',
                    'assets/vendor/bootstrap-daterangepicker/daterangepicker.js',
                    'assets/vendor/clockpicker/js/bootstrap-clockpicker.min.js',
                    'assets/vendor/jquery-asColor/jquery-asColor.min.js',
                    'assets/vendor/jquery-asGradient/jquery-asGradient.min.js',
                    'assets/vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js',
                    'assets/vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js',
                    'assets/vendor/pickadate/picker.js',
                    'assets/vendor/pickadate/picker.time.js',
                    'assets/vendor/pickadate/picker.date.js',
                    'assets/js/plugins-init/bs-daterange-picker-init.js',
                    'assets/js/plugins-init/clock-picker-init.js',
                    'assets/js/plugins-init/jquery-asColorPicker.init.js',
                    'assets/js/plugins-init/material-date-picker-init.js',
                    'assets/js/plugins-init/pickadate-init.js',
                ],
                'TrainingsIndexCertificates' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/certificates_module/init.js?v=' . $time,
                    'assets/certificates_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'GetTrainingShare' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/certificates_module/init.js?v=' . $time,
                    'assets/certificates_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'WebinarsIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_module/init.js?v=' . $time,
                    'assets/training_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'shop_module' => [
                    'assets/vendor/peity/jquery.peity.min.js',
                    'assets/vendor/owl-carousel/owl.carousel.js',
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/shop_module/init.js?v=' . $time,
                    'assets/shop_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'ia_module' => [
                    'assets/vendor/peity/jquery.peity.min.js',
                    'assets/vendor/owl-carousel/owl.carousel.js',
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/ia_module/init.js?v=' . $time,
                    'assets/ia_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'IndexHistorical' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/moment/moment.min.js',
                    'assets/historical/init.js?v=' . $time,
                    'assets/historical/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'IndexReports' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/moment/moment.min.js',
                    'assets/reports/init.js?v=' . $time,
                    'assets/reports/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'shopping_car' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/lightgallery/js/lightgallery-all.min.js',
                    'assets/vendor/moment/moment.min.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js'),
                ],
                'IndexAdminTrainings' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/administration/virtual_training/init.js?v=' . $time,
                    'assets/administration/virtual_training/main.js?v=' . $time,
                    'assets/js/main.js',
                    'assets/js/deznav-init.js'
                ],
                'TrainingsAdminIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/training_admin_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'TrainingsAdminIndexNew' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    mix('/js/app.js'),

                    'assets/vendor/moment/moment.min.js',
                    'assets/vendor/bootstrap-daterangepicker/daterangepicker.js',
                    'assets/vendor/clockpicker/js/bootstrap-clockpicker.min.js',
                    'assets/vendor/jquery-asColor/jquery-asColor.min.js',
                    'assets/vendor/jquery-asGradient/jquery-asGradient.min.js',
                    'assets/vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js',
                    'assets/vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js',
                    'assets/vendor/pickadate/picker.js',
                    'assets/vendor/pickadate/picker.time.js',
                    'assets/vendor/pickadate/picker.date.js',
                    'assets/js/plugins-init/bs-daterange-picker-init.js',
                    'assets/js/plugins-init/clock-picker-init.js',
                    'assets/js/plugins-init/jquery-asColorPicker.init.js',
                    'assets/js/plugins-init/material-date-picker-init.js',
                    'assets/js/plugins-init/pickadate-init.js',
                ],
                'event' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/datatables/js/jquery.dataTables.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'event_detail' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/chart.js/Chart.bundle.min.js',
                    'vendor/peity/jquery.peity.min.js',
                    'vendor/apexchart/apexchart.js',
                    'js/dashboard/event-detail.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'customers' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/datatables/js/jquery.dataTables.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'analytics' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/chart.js/Chart.bundle.min.js',
                    'vendor/peity/jquery.peity.min.js',
                    'vendor/apexchart/apexchart.js',
                    'js/dashboard/analytics.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'reviews' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/datatables/js/jquery.dataTables.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'app_calender' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/jqueryui/js/jquery-ui.min.js',
                    'vendor/moment/moment.min.js',
                    'vendor/fullcalendar/js/fullcalendar.min.js',
                    'js/plugins-init/fullcalendar-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'app_profile' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/lightgallery/js/lightgallery-all.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'post_details' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/lightgallery/js/lightgallery-all.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'chart_chartist' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/chartist/js/chartist.min.js',
                    'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
                    'js/plugins-init/chartist-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'chart_chartjs' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/chart.js/Chart.bundle.min.js',
                    'js/plugins-init/chartjs-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'chart_flot' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/flot/jquery.flot.js',
                    'vendor/flot/jquery.flot.pie.js',
                    'vendor/flot/jquery.flot.resize.js',
                    'vendor/flot-spline/jquery.flot.spline.min.js',
                    'js/plugins-init/flot-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'chart_morris' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                    'vendor/raphael/raphael.min.js',
                    'vendor/morris/morris.min.js',
                    'js/plugins-init/morris-init.js',
                ],
                'chart_peity' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/peity/jquery.peity.min.js',
                    'js/plugins-init/piety-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',

                ],
                'chart_sparkline' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/jquery-sparkline/jquery.sparkline.min.js',
                    'js/plugins-init/sparkline-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ecom_checkout' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/highlightjs/highlight.pack.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ecom_customers' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/highlightjs/highlight.pack.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ecom_invoice' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/highlightjs/highlight.pack.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ecom_product_detail' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/highlightjs/highlight.pack.min.js',
                    'vendor/star-rating/jquery.star-rating-svg.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ecom_product_grid' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/highlightjs/highlight.pack.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ecom_product_list' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/highlightjs/highlight.pack.min.js',
                    'vendor/star-rating/jquery.star-rating-svg.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ecom_product_order' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/highlightjs/highlight.pack.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'email_compose' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/dropzone/dist/dropzone.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'email_inbox' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'email_read' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'form_editor_summernote' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/summernote/js/summernote.min.js',
                    'js/plugins-init/summernote-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'form_element' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'form_pickers' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/moment/moment.min.js',
                    'vendor/bootstrap-daterangepicker/daterangepicker.js',
                    'vendor/clockpicker/js/bootstrap-clockpicker.min.js',
                    'vendor/jquery-asColor/jquery-asColor.min.js',
                    'vendor/jquery-asGradient/jquery-asGradient.min.js',
                    'vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js',
                    'vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js',
                    'vendor/pickadate/picker.js',
                    'vendor/pickadate/picker.time.js',
                    'vendor/pickadate/picker.date.js',
                    'js/plugins-init/bs-daterange-picker-init.js',
                    'js/plugins-init/clock-picker-init.js',
                    'js/plugins-init/jquery-asColorPicker.init.js',
                    'js/plugins-init/material-date-picker-init.js',
                    'js/plugins-init/pickadate-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'form_validation_jquery' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/jquery-validation/jquery.validate.min.js',
                    'js/plugins-init/jquery.validate-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'form_wizard' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/jquery-steps/build/jquery.steps.min.js',
                    'vendor/jquery-validation/jquery.validate.min.js',
                    'js/plugins-init/jquery.validate-init.js',
                    'vendor/jquery-smartwizard/dist/js/jquery.smartWizard.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'map_jqvmap' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/jqvmap/js/jquery.vmap.min.js',
                    'vendor/jqvmap/js/jquery.vmap.world.js',
                    'vendor/jqvmap/js/jquery.vmap.usa.js',
                    'js/plugins-init/jqvmap-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'page_error_400' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'page_error_403' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'page_error_404' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'page_error_500' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'page_error_503' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'page_forgot_password' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'page_lock_screen' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/deznav/deznav.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'LoginView' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'RecoverPasswordView' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    'assets/js/main.js?v=' . $time,
                    mix('/js/app.js')
                ],
                'NewPasswordView' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    'assets/js/main.js?v=' . $time,
                    mix('/js/app.js')
                ],
                'RegisterView' => [
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'RegisterLiderGrupoEmpresaView' => [
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'RegisterColaboradorView' => [
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'RegisterAsistView' => [
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'RegisterAsistAsistidaView' => [
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/js/plugins-init/toastr-init.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'IndexMyOrganization' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/training_admin_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'IndexReportAccompaniment' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/training_admin_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'IndexAccompaniment' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/training_admin_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'IndexReportTraining' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/training_admin_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'IndexReportTrainingAsistidas' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'detalleAuditoria' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/training_admin_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'MyClientsIndex' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/vendor/select2/js/select2.full.min.js',
                    'assets/js/plugins-init/select2-init.js',
                    'assets/vendor/toastr/js/toastr.min.js',
                    'assets/vendor/sweetalert2/dist/sweetalert2.min.js',
                    'assets/training_admin_module/init.js?v=' . $time,
                    'assets/training_admin_module/main.js?v=' . $time,
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'RecoverPassword' => [
                    'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'assets/js/custom.js',
                    'assets/js/deznav-init.js',
                    mix('/js/app.js')
                ],
                'table_bootstrap_basic' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'table_datatable_basic' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/datatables/js/jquery.dataTables.min.js',
                    'js/plugins-init/datatables.init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'uc_lightgallery' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/lightgallery/js/lightgallery-all.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'uc_nestable' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/nestable2/js/jquery.nestable.min.js',
                    'js/plugins-init/nestable-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'uc_noui_slider' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/nouislider/nouislider.min.js',
                    'vendor/wnumb/wNumb.js',
                    'js/plugins-init/nouislider-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'uc_select2' => [
                    'vendor/select2/js/select2.full.min.js',
                    'js/plugins-init/select2-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'uc_sweetalert' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/sweetalert2/dist/sweetalert2.min.js',
                    'js/plugins-init/sweetalert.init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'uc_toastr' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/toastr/js/toastr.min.js',
                    'js/plugins-init/toastr-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_accordion' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_alert' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_badge' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_button' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_button_group' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_card' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_carousel' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_dropdown' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_grid' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_list_group' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_media_object' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_modal' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_pagination' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_popover' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_progressbar' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_tab' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'ui_typography' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ],
                'widget_basic' => [
                    'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                    'vendor/chartist/js/chartist.min.js',
                    'vendor/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js',
                    'vendor/flot/jquery.flot.js',
                    'vendor/flot/jquery.flot.pie.js',
                    'vendor/flot/jquery.flot.resize.js',
                    'vendor/flot-spline/jquery.flot.spline.min.js',
                    'vendor/jquery-sparkline/jquery.sparkline.min.js',
                    'js/plugins-init/sparkline-init.js',
                    'vendor/peity/jquery.peity.min.js',
                    'js/plugins-init/piety-init.js',
                    'vendor/chart.js/Chart.bundle.min.js',
                    'js/plugins-init/widgets-script-init.js',
                    'js/custom.js',
                    'js/deznav-init.js',
                ]

            ]
        ],
    ]
];
