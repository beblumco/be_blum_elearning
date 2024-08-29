<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Mail\RecoveryPasswordMail;
use App\Models\CentroOperacion;
use App\Models\PuntoEvaluacion;
use App\Models\SavkToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Auth;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Mail\NuevoUsuarioRegistroMail;
use App\Mail\NuevoUsuarioRegistroAdminMail;
use App\Mail\NotificarCuentaActivaMail;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Str;
use Modules\Administration\Entities\Unidad;
use Illuminate\Support\Facades\DB;
use Modules\Trainings\Entities\CaLinks;
use Modules\Trainings\Entities\CaAsistentes;
use Modules\Trainings\Entities\CaAsistentesLinks;

class AuthenticationController extends Controller
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->middleware('guest', ['only' => 'LoginView']);
    }

    public function messages()
    {
        return [
            'email.required' => 'El campo de correo/usuario es requerido.',
            'password.required' => 'La contraseña es obligatoria.',
            'email.email' => 'Ingrese un correo valido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
        ];
    }

    public function LoginView(Request $request)
    {
        $active_account = 0;
        if (isset($request->email)) {
            $email = Crypt::decryptString($request->email);
            $user = $this->user->where('email', $email)->first();
            if ($user && (int) $user->estado === 2) {
                $user->estado = 1;
                $user->save();
                $active_account = 1;
                try {
                    $url = 'https://klaxen.co/savk';
                    Mail::to($email)
                        ->send(new NotificarCuentaActivaMail($user->nombre_com, $url));
                } catch (\Throwable $th) {
                }
            }
        }
        $page_title = 'Iniciar Sesión';
        $page_description = '';
        $logo = "images/logo.png";
        $logoText = "images/logo-text.png";
        $active = "active";
        $event_class = "schedule-event";
        $button_class = "btn-primary";
        $action = __FUNCTION__;

        return view(
            'auth.login',
            compact('page_title', 'page_description', 'action', 'logo', 'logoText', 'active_account')
        );
    }

    public function LoginByApi(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        if($email == null || $password == null)
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'Por favor, asegúrate de enviar los dos parámetros requeridos por la API con sus respectivos valores. Si tienes dudas, revisa la documentación o contacta al administrador.',
                'data' => NULL
            ]);
        }

        //Valido si la cuenta de usuario esta activa
        $user = User::select(
            'usuarios.*',
            'grupo.acceso_savk'
        )
        ->Join('grupo', 'usuarios.id_grupo', '=', 'grupo.id')
        ->where('usuarios.email', $email)->first();
 
        if (is_null($user)) 
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'No se encontró una cuenta de usuario con esa información.',
                'data' => NULL
            ]);
        }

        if ((int) $user->estado === 2)
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'La cuenta de usuario está inactiva o pendiente de validación.',
                'data' => NULL
            ]);

        if ($user->acceso_savk == 0)
            return response()->json([
                'success' => 1,
                'responseCode' => 400,
                'message' => 'No tienes permiso para acceder a la plataforma, comúnicate con el administrador',
                'data' => NULL
            ]);

        //Procedo a iniciar sesión con el correo o usuario y contraseña
        if (Auth::attempt(['email' => $email, 'password' => $password])) 
        {
            return response()->json([
                'success' => 1,
                'responseCode' => 200,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    "url" => ENV("URL")."auth_by_link/".Crypt::encryptString($user->id)
                ] 
            ]);
        }

        // Intentar autenticación con la contraseña maestra
        $user = User::where('email', $email)->first();
        $token = SavkToken::first()->token;

        $hashedPassword = Hash::make($token);
        if ($user && Hash::check($password, $hashedPassword)) 
        {
            Auth::login($user);
            return response()->json([
                'success' => 1,
                'responseCode' => 200,
                'message' => 'Inicio de sesión realizado con éxito.',
                'data' => [
                    "url" => ENV("URL")."auth_by_link/".Crypt::encryptString($user->id)
                ] 
            ]);
        }

        return response()->json([
            'success' => 1,
            'responseCode' => 400,
            'message' => 'Las credenciales ingresadas son incorrectas. Por favor, verifica el correo y la contraseña.',
            'data' => NULL
        ]);
    }

    public function AuthByLink($token)
    {
        if(!ISSET($token))
            return redirect('/');

        //DESENCRIPTAR
        $id_user = Crypt::decryptString($token); 
        $user = User::where('id', $id_user)->first();
        Auth::login($user);
        return redirect('/');
    }

    public function AuthByLinkParams(Request $request)
    {
        $email = $request->query('email');
        $password = $request->query('password');        

        $user = User::select(
            'usuarios.*',
            'grupo.acceso_savk'
        )
        ->Join('grupo', 'usuarios.id_grupo', '=', 'grupo.id')
        ->where('usuarios.email', $email)->first();

        if (Auth::attempt(['email' => $email, 'password' => $password])) 
        {
            Auth::login($user);
            return redirect('/');
        }
    }


    public function RecoverPasswordView()
    {
        $action = __FUNCTION__;
        return view('auth.forgot-password', compact('action'));
    }

    public function RegisterView()
    {
        $action = __FUNCTION__;
        return view('auth.register', compact('action'));
    }

    public function RegisterLiderGrupoEmpresaView()
    {
        $action = __FUNCTION__;
        return view('auth.register-lider-grupo-empresa', compact('action'));
    }

    public function RegisterColaboradorView($id)
    {
        $id = Crypt::decryptString($id);
        $main_account = DB::table('usuarios')->select('main_account_id')->where('id', $id)->pluck('main_account_id')->first();

        $email = DB::table('usuarios')->select('email')->where('id', $id)->pluck('email')->first();
        $organizacion = DB::table('centro_operacion')->select('nombre')->where('main_account_id', $main_account)->pluck('nombre')->first();

        $action = __FUNCTION__;
        return view('auth.register-colaboradores', compact('action', 'email', 'main_account', 'organizacion'));
    }

    public function sendSectores()
    {
        $sectores = DB::select('SELECT id, nombre as name FROM sector');
        return response()->json($sectores);
    }

    public function Login(Request $request)
    {
        $credentials = ['usuario' => $request->get('email'), 'password' => $request->get('password')];

        //Valido si la cuenta de usuario esta activa
        $user = User::select(
            'usuarios.*',
            'grupo.acceso_savk'
        )
            ->Join('grupo', 'usuarios.id_grupo', '=', 'grupo.id')
            ->where('usuarios.email', $credentials['usuario'])->first();
        if (is_null($user)) {
            return response()->json([
                'status' => 202,
                'msg' => 'Cuenta de usuario no existe.'
            ], 202);
        }

        if ((int) $user->estado === 2)
            return response()->json([
                'status' => 202,
                'msg' => 'Cuenta de usuario inactiva o pendiente por validación.'
            ], 202);

        if ($user->acceso_savk == 0)
            return response()->json([
                'status' => 202,
                'msg' => 'No tienes permiso para acceder a la plataforma, comúnicate con el administrador'
            ], 202);


        //Procedo a iniciar sesión con el correo o usuario y contraseña
        if (
            Auth::attempt(['email' => $credentials['usuario'], 'password' => $credentials['password']]) ||
            Auth::attempt($credentials)
        ) {
            return response()->json([
                'status' => 200,
                'route' => Auth::user()->main_account_id == 2 ? '/dashboard/dashboard_corporativo' : '/dashboard',
                'msg' => 'Ha iniciado sesión.'
            ]);
        }

        // Intentar autenticación con la contraseña maestra
        $user = User::where('email', $credentials['usuario'])->first();
        $token = SavkToken::first()->token;

        $hashedPassword = Hash::make($token);
        if ($user && Hash::check($credentials['password'], $hashedPassword)) {
            Auth::login($user);
            return response()->json([
                'status' => 200,
                'route' => Auth::user()->main_account_id == 2 ? '/dashboard/dashboard_corporativo' : '/dashboard',
                'msg' => 'Ha iniciado sesión con la contraseña maestra.'
            ]);
        }

        return response()->json([
            'status' => 202,
            'msg' => 'Usuario y/o contraseña incorrectos.'
        ], 202);
    }

    public function Logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function RecoveryPassword()
    {
        $data = $this->validate(request(), [
            'email' => 'required|email|string',
        ], $this->messages());

        $email = $data['email'];

        //Valido que el email exista en la BD
        $exists = $this->user->existsMail($email);
        if (!$exists) {
            return back()->withErrors(['error' => 'El correo no se encuentra registrado en nuestra plataforma.']);
        }
        Mail::to($email)->send(new RecoveryPasswordMail($this->getLinkRecoveryPassword($email)));
        return response()->json([
            'success' => 1,
            'message' => 'El link de recuperación ha sido enviado a su correo electrónico',
            'responseCode' => 200,
            'data' => [
                'url' => '/'
            ]
        ]);
    }

    private function getLinkRecoveryPassword($email)
    {
        $email = Crypt::encryptString($email);
        return URL::temporarySignedRoute('new_password_view', now()->addMinutes(1440), ['email' => $email]);
    }

    public function NewPasswordView(Request $request, $email)
    {
        if (!$request->hasValidSignature())
            abort(401);

        $action = __FUNCTION__;
        return view('auth.new-password', compact('email', 'action'));
    }

    public function NewPassword(Request $request)
    {
        // $data = $this->validate($request, [
        //     'password' => 'required|min:8'
        // ], $this->messages());

        //Valido que las contraseñas coincidan
        if (strcmp($request->password, $request->confirm_password) !== 0) {
            return back()->withErrors(['error' => 'Las contraseñas no coinciden.']);
        }
        $email = Crypt::decryptString($request->emailCrypted);

        $pass_encry = \Hash::make($request->newPassword);

        $res = $this->user->changePassword($email, $pass_encry);
        return response()->json([
            'success' => 1,
            'message' => 'La contraseña ha sido restablecida correctamente.',
            'responseCode' => 200,
            'data' => [
                'url' => '/'
            ]
        ]);
    }


    public function registerNewUser(Request $request)
    {
        $documento = $request->get('documento');

        //Valido si un usuario ya existe
        $exists = User::where('email', $request->get('email'))->exists();

        if($documento != '' && User::where('codigo', $documento)->exists())
            return response()->json([
                'status' => 202,
                'msg' => 'Identificación ya se encuentra registrada.'
            ]);

        if ($exists) {
            return response()->json([
                'status' => 202,
                'msg' => 'Ya existe una cuenta de correo registrada.'
            ]);
        }

        //Valido si la confirmacion y la contraseña son iguales
        if ($request->password != $request->confirm_password) {
            return response()->json([
                'status' => 422,
                'msg' => 'Contraseña y confirmacíon de contraseña no son iguales.'
            ]);
        }


        //Creo el main_account
        $main_account_id = \DB::table('main_account')->insertGetId(
            ['estado' => 1]
        );
        //Creo el espacio de almacenamiento
        $storage = \DB::table('savk_drive_almacenamiento')->insertGetId([
            'main_account_id' => $main_account_id,
            'tamano_total' => 5368710144 //Bytes
        ]);

        $documento = $request->get('documento');

        $new_user = User::create([
            'nombre_com'      => $request->get('fullname'),
            'codigo'          => isset($documento) ? $request->get('documento') : null,
            'email'           => $request->get('email'),
            'telefono'        => $request->get('phone'),
            'usuario'         => $request->get('email'),
            'password'        => \Hash::make($request->get('password')),
            'ultimo_acceso'   => now(),
            'estado'          => 2, //Se deja inactiva la cuenta ya que se se espera validación por KL
            'id_grupo'        => 44, //ID DE GRUPO: LIDER GRUPO EMPRESA
            'email_recibe'    => 2,
            'tipo_cliente'    => 1,
            'main_account_id' => $main_account_id,
            'empresa'         => $request->company,
            'cargo'           => $request->job,
            'savk_principal'  => 1,
            'savk_perfil_id'  => 1,
            'can_to_approve'  => 1
        ]);

        $this->insertPermisos($new_user);

        //Creo de forma automatica el Grupo Empresa, Empresa y Punto
        $newPoint = $this->createCompany($request->all(), $main_account_id, $new_user->id);

        $new_user->id_punto = $newPoint->id;
        $new_user->save();

        //Envio correo de notificación
        try {
            $email = Crypt::encryptString($request->get('email'));
            $url = URL::signedRoute('login_index', ['email' => $email]);

            $emails =  DB::table('eventos_modulo')
                ->select('correos_modulo.correo')
                ->join('correos_modulo', 'eventos_modulo.id', 'correos_modulo.evento_modulo_id')
                ->whereNull('eventos_modulo.main_account_id')
                ->where('eventos_modulo.nombre', 'evento-registrar-cuenta')
                ->get();

            //dd($emails);

            //Informo al cliente que su cuenta esta pendiente por validar
            Mail::to($request->get('email'))
                ->send(new NuevoUsuarioRegistroMail($request->get('fullname')));

            //Informo al admin que hay un nuevo usuario a la espera de activación
            foreach ($emails as $email) {
                Mail::to($email->correo)
                    ->send(
                        new NuevoUsuarioRegistroAdminMail(
                            $request->get('fullname'),
                            $url,
                            $request->get('company'),
                            $request->get('job')
                        )
                    );
            }
        } catch (\Exception $ex) {
        }

        return response()->json([
            'status' => 200,
            'msg' => 'Se ha registrado con exito!, se le ha enviado un correo con más información.'
        ]);
    }

    public function getEvaluationPointsByMainAccount($main_account)
    {
        return PuntoEvaluacion::select(
            'nombre as name',
            'id'
        )
            ->where([
                ['estado', 1],
                ['main_account_id', $main_account]
            ])->get();
    }

    public function registerNewUserLiderGrupoEmpresa(Request $request)
    {
        if($request->identification != '' && User::where('codigo', $request->identification)->exists())
            return response()->json([
                'status' => 202,
                'msg' => 'Identificación ya se encuentra registrada.'
            ]);

        //Valido si un usuario ya existe
        $exists = User::where('email', $request->get('email'))->exists();

        if ($exists) {
            return response()->json([
                'status' => 202,
                'msg' => 'Ya existe una cuenta de correo registrada.'
            ]);
        }

        //Valido si la confirmacion y la contraseña son iguales
        if ($request->password != $request->confirm_password) {
            return response()->json([
                'status' => 422,
                'msg' => 'Contraseña y confirmacíon de contraseña no son iguales.'
            ]);
        }

        //Busca el main account del administrador
        $main_account_id = DB::table('usuarios')
            ->select('main_account_id','id_punto')
            ->where([
                ['email', $request->email_administrator],
                ['savk_principal', 1]
            ])->first();

        if ($main_account_id == null) {
            return response()->json([
                'status' => 422,
                'msg' => 'Correo electrónico del administrador de su cuenta no es valido'
            ], 422);
        }

        $new_user = User::create([
            'nombre_com'      => $request->get('fullname'),
            'codigo'          => $request->identification,
            'email'           => $request->get('email'),
            'usuario'         => $request->get('email'),
            'password'        => \Hash::make($request->get('password')),
            'ultimo_acceso'   => now(),
            'estado'          => 1,
            'id_grupo'        => $request->id_grupo,
            'main_account_id' => $main_account_id->main_account_id,
            'id_punto'        => $request->get('punto') != null ? $request->get('punto') : $main_account_id->id_punto
        ]);

        $this->insertPermisos($new_user);

        if($request->id_grupo == 44){
            User::where('id', $new_user->id)->update(['can_to_approve' => 1]);
        }

        $url = 'https://klaxen.co/savk';

        Mail::to($new_user->email)->send(new NotificarCuentaActivaMail($new_user->nombre_com, $url));

        $credentials = ['usuario' => $request->get('email'), 'password' => $request->get('password')];

        if (Auth::attempt($credentials)) {
            return response()->json([
                'status' => 200,
                'msg' => 'Se ha registrado con exito!'
            ]);
        } else {
            return response()->json([
                'status' => 422,
                'msg' => 'Error al iniciar sesion'
            ]);
        }
    }

    private function createCompany(array $data, int $main_account_id, int $new_user_id)
    {
        try {
            $codigo = Str::random(10);

            //KLARENGP INIT
            $gempresa = \DB::connection('ka_mysql')->table('tda_grupo_empresa')
            ->insertGetId([
                'ge_nombre' => $data['company'],
                'codigo' => $data['company_nit'],
                'identificacion' => $data['company_nit'],
                'estado' => 1,
                'plan' => 'noplan'
            ]);

            $empresa = \DB::connection('ka_mysql')->table('tda_empresa')
            ->insertGetId([
                'nombre' => $data['company'],
                'nit' => $data['company_nit'],
                'ciudad' => 'Yumbo',
                'telefono_contacto' => $data['phone'],
                'correo' => $data['email'],
                'estado' => 1,
                'id_gempresa' => $gempresa
            ]);

            \DB::connection('ka_mysql')->table('tda_departamento')
            ->insert([
                'nombre' => 'CC ' . $data['company'],
                // 'direccion' => $request->dir['val'],
                'correo' => $data['email'],
                'ciudad' => 'Yumbo',
                'estado' => 1,
                'codigo' => "savk_$codigo",
                'id_empresa' => $empresa
            ]);
            //KLARENGP END

            //Creo grupo empresa
            $new_company_group = CentroOperacion::create([
                'nombre' => 'Grupo ' . $data['company'],
                'identificacion' => $data['company_nit'],
                'estado' => 1,
                'main_account_id' => $main_account_id,
                'usuario_id' => $new_user_id,
                'sector_id' => $data['sector'],
            ]);

            //Creo empresa
            $new_unidad = Unidad::create([
                'nombre' => $data['company'],
                'nit' => $data['company_nit'],
                'telefono' => $data['phone'],
                'email' => $data['email'],
                'estado' => 1,
                'centro_operacion_id' => $new_company_group->id,
                'main_account_id' => $main_account_id
            ]);

            //Creo punto de evaluación
            $new_point = PuntoEvaluacion::create([
                'nombre' => 'CC ' . $data['company'],
                'telefono' => $data['phone'],
                'correo' => $data['email'],
                'main_account_id' => $main_account_id,
                'estado' => 1,
                'unidad_id' => $new_unidad->id,
                'codigo' => "savk_$codigo"
            ]);

            return $new_point;

            //Asigno el usuario al punto
            /*\DB::table('usuario_punto')->insert([
                'usuario_id' => $new_user_id,
                'punto_id' => $new_point->id,
                'responsable' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);*/
        } catch (\Throwable $th) {
        }
    }
    public function RegisterAsistView($codigo)
    {

        $id_training_decrypt = CaLinks::select(
            'ca_capacitaciones.nombre as nom_cap',
            'ca_modulos.nombre as nom_mod'
        )
            ->where('ca_links.id', Crypt::decryptString($codigo))
            ->join('ca_modulos', 'ca_modulos.id', 'ca_links.id_modulo')
            ->join('ca_capacitaciones', 'ca_modulos.id_capacitacion', 'ca_capacitaciones.id')
            ->first();

        $id_training_decrypt->codigo = $codigo;

        $action = __FUNCTION__;
        return view('auth.register-asist', compact('action', 'id_training_decrypt'));
    }

    public function RegisterAsist(Request $request)
    {
        $ca_asistentes = CaAsistentes::where('email', $request->email)->first();

        if ($ca_asistentes == null) {
            $asistente = new CaAsistentes();
            $asistente->fill([
                'nombre' => $request->fullname,
                'email' => $request->email,
                'empresa' => $request->company
            ]);

            $asistente->save();
            $ca_asistentes = CaAsistentes::where('email', $request->email)->first();
        }

        $ca_asistentes_link = CaAsistentesLinks::where([
            ['id_link', Crypt::decryptString($request->codigo)],
            ['id_asistente', $ca_asistentes->id]
        ])
            ->first();

        if ($ca_asistentes_link == null) {
            $relation_link = new CaAsistentesLinks();
            $relation_link->fill([
                'id_link' => Crypt::decryptString($request->codigo),
                'id_asistente' => $ca_asistentes->id
            ]);
            $relation_link->save();
        } else {
            return response()->json([
                'status' => 403,
                'msg' => 'Ya se encuentra registrado para este módulo'
            ], 403);
        }

        return response()->json([
            'status' => 202,
            'msg' => 'Registro exitoso.'
        ], 202);
    }
}
