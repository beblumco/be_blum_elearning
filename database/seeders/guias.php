<?php

namespace Database\Seeders;
use App\Models\Guia;

use Illuminate\Database\Seeder;

class guias extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Guia::insert([
            [
                'titulo' => 'Esta es tu página de inicio',
                'descripcion' => 'Aquí podrás encontrar todas las cifras de ahorro y de la propuesta de valor',
                'id_elemento' => 'dev_container_menu_1',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Primer indicador',
                'descripcion' => 'Aquí podrás ver todo el aporte que haces al medio ambiente como aliado KLAXEN',
                'id_elemento' => 'indicator_sost',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Entrenamiento',
                'descripcion' => 'Estas son las horas de entrenamiento que KLAXEN te ha entregado y el avance de tu plan.',
                'id_elemento' => 'indicator_ent',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Indicadores de todo el equipo',
                'descripcion' => 'Aquí puedes encontrar, la cantidad de horas y certificados de entrenamiento entregados a tu equipo.',
                'id_elemento' => 'btn-ver-indicadores',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Gestiona tus usuarios',
                'descripcion' => 'Aquí puedes crear o invitar a todos los usuarios, definir perfiles y asignar su centro de costo.',
                'id_elemento' => 'dev_container_menu_2',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Capacitaciones',
                'descripcion' => 'Aquí puedes iniciar tu entrenamiento con capacitaciones de tu plan LyD y las creadas por el administrador.',
                'id_elemento' => 'dev_container_menu_6',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Mi organización',
                'descripcion' => 'Aquí podrás gestionar usuarios, perfiles y asignarlos a cada centro de costo o empresa.',
                'id_elemento' => 'container_bnts',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Invitar usuarios',
                'descripcion' => 'Copia el link y compártelo a tu equipo para facilitar su registro.',
                'id_elemento' => 'btn-org-menu-invitar-usuarios',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Mi plan de limpieza y desinfección',
                'descripcion' => 'Aquí puedes capacitarte en el plan L&D, capacitaciones internas y descargar los certificados.',
                'id_elemento' => 'dv-menu-elearning',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Mis capacitaciones',
                'descripcion' => 'Aquí puedes crear tus propias capacitaciones y compartirlas con todo tu equipo de trabajo.',
                'id_elemento' => 'btn-elearning-menu-crear-curso',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Iniciar capacitación',
                'descripcion' => 'Presiona este botón para ver todo el contenido del curso.',
                'id_elemento' => 'btnIniciar',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Capacitaciones asistidas',
                'descripcion' => 'Ingresa aquí y crea una capacitación asistida virtual o presencial.',
                'id_elemento' => 'btn-asistida-crear',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Reporte PDF',
                'descripcion' => 'Descarga aquí el reporte completo de la visita.',
                'id_elemento' => 'btn-asistida-reporte-0',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Asistentes',
                'descripcion' => 'Carga tus asistentes y descarga sus certificados.',
                'id_elemento' => 'btn-asistida-asistentes-0',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Link',
                'descripcion' => 'Genera el link para el registro y certificación de tus asistentes.',
                'id_elemento' => 'btn-asistida-link-0',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Webinars',
                'descripcion' => 'Consulta los webinars de tu sector e inscríbete en la agenda de eventos para próximos webinars.',
                'id_elemento' => 'dv-menu-webinars',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Inscripción de webinars',
                'descripcion' => 'Confirma tu inscripción y recibe toda la información para asistir al evento.',
                'id_elemento' => 'btnAgendarWebinar',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Iniciar webirnar',
                'descripcion' => 'Visualiza todo el contenido de webinar y adquiere tu certificado.',
                'id_elemento' => 'btnIniciarWebinar',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Tipo de capacitación',
                'descripcion' => 'Escoge la modalidad de capacitación, privada para un cliente o sector específico y pública para asistentes en general.',
                'id_elemento' => 'dv-crear-tipo-cap',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Generación de certificado',
                'descripcion' => 'Selecciona si aplica el certificado para la capacitación de formal general o por módulo',
                'id_elemento' => 'dv-crear-certificara-cap',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Puntos',
                'descripcion' => 'Ingresa la cantidad de puntos que el usuario obtendrá por completar la capacitación.',
                'id_elemento' => 'dv-crear-puntos-cap',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Evaluación',
                'descripcion' => 'Selecciona si el curso tendrá una evaluación general o por módulo.',
                'id_elemento' => 'dv-crear-evaluara-cap',
                'posicion_mensaje' => 'right-start',
                'tipo' => '1',
                ],
                [
                'titulo' => 'Módulos',
                'descripcion' => 'Presiona aquí para agregar los módulos o categorías del contenido de la capacitación.',
                'id_elemento' => 'dv-crear-agregar-mod-cap',
                'posicion_mensaje' => 'right-start',
                'tipo' => '2',
                ],
                [
                'titulo' => 'Video',
                'descripcion' => 'Presion aquí y agrega un video de Youtube pegando la url.',
                'id_elemento' => 'dv-crear-video-cap-0',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '2',
                ],
                [
                'titulo' => 'Recursos de apoyo',
                'descripcion' => 'Adjunta aquí PDF e imágenes que servirán de apoyo para los estudiantes.',
                'id_elemento' => 'dv-crear-recursos-cap-0',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '2',
                ],
                [
                'titulo' => 'Evaluación',
                'descripcion' => 'Crea la evaluación de tu capacitación ingresando las preguntas y sus posibles respuestas.',
                'id_elemento' => 'dv-crear-evaluacion-cap-0',
                'posicion_mensaje' => 'bottom-end',
                'tipo' => '2',
                ],
        ]);
    }
}
