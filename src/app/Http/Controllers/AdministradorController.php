<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrador;
use Illuminate\Database\QueryException;

class AdministradorController extends Controller
{
    //
    /**
     * Listado de todos los administradores en orden alfabético
     * GET      /api/administrador    administrador.index
     */
    public function index()
    {
        $administradores = Administrador::orderBy('usuario', 'asc')->get();

        return $administradores;
    }

    /**
     * Insertar nueva administrador en base de datos
     * POST 	/api/administrador     administrador.store
     * @param Request $request
     * @return Json|String
     */
    public function store(Request $request) 	
    {
        // comprobar campos
        if (strlen(trim($request->dato)) == 0)
        {
            return response()->json(['error' => 'El campo usuario no puede tener un valor vacío.']);
        }

        // Crear objeto Administrador con los datos recibidos
        $admin = new Administrador();
        $admin->usuario = $request->dato;

        try
        {
            $admin->save(); // guardar en base de datos
        }
        catch (QueryException $e) // error en la operación
        {
            if ($e->getCode() == 23000) // restricción de unicidad de nombre
            {
                return response()->json(['error' => 'Ya existe un usuario con ese nombre.']);
            }
            else
            {
                return response()->json(['error' => 'Error al insertar usuario en base de datos.']);
            }
        }

        return $admin;
    }

    /**
     * Eliminar usuario de listado de administradores
     * DELETE 	/api/administrador/{id}      administrador.destroy
     * @param int $id   id del usuario en base de datos
     * @return void
     */
    public function destroy(int $id) 	
    {
        $adm = Administrador::find($id);
        $adm->delete();
    }
}
