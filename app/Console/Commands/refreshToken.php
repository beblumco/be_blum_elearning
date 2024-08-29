<?php

namespace App\Console\Commands;

use App\Models\SavkToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class refreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el token de Savak';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $password = Str::random(16);
        SavkToken::query()->delete();
        SavkToken::create([
            'token' => $password
        ]);
        echo 'Creado correctamente';
    }
}
