<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrador;
use Illuminate\Database\QueryException;

class NuevoAdministrador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrador:nuevo {usuario : identificador de usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna permisos de administrador a un usuario';

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
        $usu = $this->argument('usuario');
        
        if ($usu == null)
        {
            $this->warn('Debe indicar el nombre de usuario.');
            return -1;
        } 
        if (strlen($usu) > 60)
        {
            $this->warn('El nombre de usuario es demasiado largo.');
            return -1;
        } 

        // Crear objeto Administrador con los datos recibidos
        $admin = new Administrador();
        $admin->usuario = $usu;

        try
        {
            $admin->save(); // guardar en base de datos
        }
        catch (QueryException $e) // error en la operación
        {
            if ($e->getCode() == 23000) // restricción de unicidad de nombre
            {
                $this->error('Ya existe un usuario con ese nombre.');
            }
            else
            {
                $this->error('Error al insertar usuario en base de datos.');
            }
            return -1;
        }

        return 0;
    }
}
