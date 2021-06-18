<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Listado de todos las categorías en orden inverso de insercción en base de datos
     * GET      /api/categoria    categoria.index
     */
    public function index()
    {
        return Categoria::orderBy('created_at', 'desc')->get();
    }

    /**
     * Insertar nueva categoria en base de datos
     * POST 	/api/categoria      categoria.store
     * @param Request $request
     * @return Json|Categoria
     */
    public function store(Request $request) 	
    {
        // Crear objeto Categoría con los datos recibidos
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->des;
        $categoria->visible = true;

        try
        {
            $categoria->save(); // guardar en base de datos
        }
        catch (QueryException $e) // error en la operación
        {
            if ($e->getCode() == 23000) // restricción de unicidad de nombre
            {
                return response()->json(['error' => 'Ya existe una categoría con es nombre.']);
            }
            else
            {
                return response()->json(['error' => 'Error al insertar categoría en base de datos.']);
            }
        }

        return $categoria;
    }

    /**
     * Cambiar estado de visibilidad de una categoría
     * PUT /api/categoria/cambiovisibilidad/{idcategoria}
     * @param Request $request
     * @param int $id   id de categoria en base de datos
     * @return Json|Categoria
     */
    public function cambiarVisible(Request $request, $id)
    {
        // recuperar datos de categoría
        $categoria = Categoria::find($id);
        if ($categoria == null) return response()->json(['error' => 'Ya no existe la categoría.']);

        // invertir valor de visibilidad
        if ($categoria->visible) $categoria->visible = false;
        else $categoria->visible = true;

        try
        {
            $categoria->save(); // guardar en base de datos
        }
        catch (QueryException $e) // error en la operación
        {
            return response()->json(['error' => 'Error al modificar categoría en base de datos.']);
        }

        return $categoria;
    }

    /**
     * Modificar datos de categoría
     * PUT 	/api/categoria/{idcategoria} 	categoria.update
     * @param Request $request
     * @param int $id   id de categoria en base de datos
     * @return Json|Categoria
     */
    public function update(Request $request, $id)
    {
        // cargar datos de categoría
        $categoria = Categoria::find($id);
        if ($categoria == null) return response()->json(['error' => 'Ya no existe la categoría.']);

        // asignar nuevos valores
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->des;

        try
        {
            $categoria->save(); // guardar en base de datos
        }
        catch (QueryException $e) // error en la operación
        {
            if ($e->getCode() == 23000) // restricción de unicidad de nombre
            {
                return response()->json(['error' => 'Ya existe una categoría con es nombre.']);
            }
            else
            {
                return response()->json(['error' => 'Error al modificar categoría en base de datos.']);
            }
        }

        return $categoria;
    }

    /**
     * Eliminar categoría
     * DELETE 	/api/categoria/{idcategoria}      categoria.destroy
     * @param int $id   id de categoria en base de datos
     * @return void
     */
    public function destroy(int $id) 	
    {
        $categoria = Categoria::find($id);
        $categoria->delete();
    }
}
