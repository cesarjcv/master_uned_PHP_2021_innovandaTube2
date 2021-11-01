<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Video;

use Illuminate\Support\Facades\Log;

class CategoriaController extends Controller
{
    /**
     * Listado de todos las categorías en orden alfabético en base de datos
     * GET      /api/categoria    categoria.index
     */
    public function index()
    {
        return Categoria::orderBy('nombre', 'asc')->get();
    }

    /**
     * Insertar nueva categoria en base de datos
     * POST 	/api/categoria      categoria.store
     * @param Request $request
     * @return Json|Categoria
     */
    public function store(Request $request) 	
    {
        // comprobar campos
        if (strlen(trim($request->nombre)) == 0)
        {
            return response()->json(['error' => 'El campo nombre no puede tener un valor vacío.']);
        }
        if (!preg_match("/^(http(s)?:\/\/[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9]{1,10}(\/[-a-zA-Z0-9()@:%_\+.~#?&=]*)*)?$/i", $request->url))
        {
            return response()->json(['error' => 'El formato del camppo URL no es correcto.']);
        }
        // Crear objeto Categoría con los datos recibidos
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->des;
        $categoria->url = $request->url;
        $categoria->visible = true;

        try
        {
            $categoria->save(); // guardar en base de datos
        }
        catch (QueryException $e) // error en la operación
        {
            if ($e->getCode() == 23000) // restricción de unicidad de nombre
            {
                return response()->json(['error' => 'Ya existe una categoría con ese nombre.']);
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
        // comprobar campos
        if (strlen(trim($request->nombre)) == 0)
        {
            return response()->json(['error' => 'El campo nombre no puede tener un valor vacío.']);
        }
        if (!preg_match("/^(http(s)?:\/\/[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9]{1,10}(\/[-a-zA-Z0-9()@:%_\+.~#?&=]*)*)?$/i", $request->url))
        {
            return response()->json(['error' => 'El formato del camppo URL no es correcto.']);
        }

        // cargar datos de categoría
        $categoria = Categoria::find($id);
        if ($categoria == null) return response()->json(['error' => 'Ya no existe la categoría.']);

        // asignar nuevos valores
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->des;
        $categoria->url = $request->url;

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

    /**
     * Listado de videos por categorías solicitadas
     * GET /api/categoria/{idcategoria}/videos
     * @param Request $request
     * @param int $id id de categoría
     * @return Video[]
     */
    public function videosPorCategoria(Request $request, $id)
    {
        // videos con el id de categoría y con el campo deleted_at nulo (no eliminados), y estado 'visible'
        $vcat = DB::table('videocategorias')->select('videos.id as id', 'videoid', 'titulo', 'descripcion', 'imagen', 'proporcion', 'duracion', 'estrep', 'estgusta', 'estnogusta', 'fecha', 'estrellas')
        ->leftJoin('videos', 'videocategorias.idvideo', '=', 'videos.id')->where('idcategoria', $id)->whereNull('videos.deleted_at')->where('visible', true); 

        // vídeos con categoría de canal
        return DB::table('canalcategorias')->select('videos.id as id', 'videoid', 'titulo', 'videos.descripcion as descripcion', 'videos.imagen as imagen', 'proporcion', 'duracion', 'estrep', 'estgusta', 'estnogusta', 'videos.fecha as fecha', 'estrellas')
        ->leftJoin('canals', 'canals.id', '=', 'canalcategorias.idcanal')->leftJoin('lista_reproduccions', 'canals.id', '=', 'lista_reproduccions.idcanal')
        ->leftJoin('videos', 'lista_reproduccions.id', '=', 'videos.idlistarep')->where('idcategoria', $id)->whereNull('videos.deleted_at')->where('visible', true)->union($vcat)->get(); 
    }

    /**
     * Categorias por id
     * PUT /api/categoria/porid
     * @param Request $request
     * 
     * @return Categoria
     */
    public function categoriasPorID(Request $request)
    {
        return Categoria::whereIn('id', $request->listas)->get();
    }

    /**
     * Categorias con video
     * PUT /api/categoria/convideo
     * @return Categoria[]
     */
    public function categoriasConVideo()
    {
        /*return*/$vcat = Categoria::join('videocategorias', 'categorias.id', '=', 'videocategorias.idcategoria')
        ->leftJoin('videos', 'videocategorias.idvideo', '=', 'videos.id')->where('categorias.visible', true)->whereNull('videos.deleted_at')->where('videos.visible', true)
        ->select('categorias.id', 'categorias.nombre', 'categorias.descripcion')->distinct();

        return Categoria::join('canalcategorias', 'categorias.id', '=', 'canalcategorias.idcategoria')->where('categorias.visible', true)
        ->select('categorias.id', 'categorias.nombre', 'categorias.descripcion')->distinct()->union($vcat)->get();
    }
}
