<?php
$time = time();
    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN PARA LA PÁGINA DASHBOARD (COLOSENSES 3:23)
    |--------------------------------------------------------------------------
    | `Y todo lo que hagáis, hacedlo de corazón, como para el Señor, y no para los hombres`
    |
    | Este archivo contiene la configuración de los assets que necesita para funciona
    | comenzando por los estilos generales como los estilos de carouseles, selects que
    | necesite la página, incluyendo el CSS personalizado o el JS personalizado.
    |
    */

return [
    'name' => 'Savk',
    'public' => [
        'global' => [
              'css' => [
                'assets/css/style.css',
                'https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap',
              ],
              'js' => [
                'assets/vendor/global/global.min.js',
              ],
		    ],
        'pagelevel' => [
                'css' => [
                    'DashboardPrincipalIndex' => [
                          'assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
                          'assets/vendor/chartist/css/chartist.min.css',
                          'assets/vendor/owl-carousel/owl.carousel.css',
                    ]
                ],
                'js' => [
                    'DashboardPrincipalIndex' => [
                          'assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
                          'assets/vendor/chart.js/Chart.bundle.min.js',
                          'assets/vendor/owl-carousel/owl.carousel.js',
                          'assets/vendor/peity/jquery.peity.min.js',
                          'assets/vendor/apexchart/apexchart.js',
                          /* 'assets/js/dashboard/dashboard-1.js', */ //GRAFICAS
                          'assets/js/module_dashboard_principal/init.js?v='.$time,
                          'assets/js/module_dashboard_principal/main.js?v='.$time,
                          'assets/js/deznav-init.js',
                    ]
                ]
        ]
    ]
    
];
