<template>
    <div>
        <div class='anadir'>
            <button class="btn btn-primary" @click="abrirNuevaCat()">Añadir categoría</button>
        </div>
        
        <dialogo-nueva-categoria-componente identificador="dialogoNuevo" v-on:nuevaCat="nuevaCat" ></dialogo-nueva-categoria-componente>

        <dialogo-mod-categoria-componente identificador="dialogoModificar" :categoria="catMod" v-on:modCat="modCat" ></dialogo-mod-categoria-componente>
        
        <dialogo-confirmacion-componente id="conf" texto="¿Seguro que quiere eliminar esta categoría?"
            v-on:respuesta="respuestaEliminar"></dialogo-confirmacion-componente>

        <div class="container">
            <div class="row row-cols-auto">
                <entrada-categoria-componente v-for="categoria in categorias" :key="categoria.id" :categoria="categoria" v-on:eliminar="eliminar" v-on:modificar="abrirModCat" v-on:cambiovisible="cambioVisibilidad">
                </entrada-categoria-componente>
            </div>
        </div>
    </div>
</template>

<script>
import DialogoConfirmacionComponente from './DialogoConfirmacionComponente.vue';

    export default {
  components: { DialogoConfirmacionComponente },

        data(){
            return {
                categorias: [], // listado de categorias
                idCatEliminar: null, // id de categoría a eliminar
                catMod: null // categoría para ventana de modificar
            }
        },
        mounted() {
            // obtener listado de categorías
            axios.get('/api/categoria').then((response) => 
            {
                    this.categorias = response.data;
            });
        },
        methods: {
            /**
             * Abrir ventana para nueva categoría
             */
            abrirNuevaCat() {
                // abrir ventana modal
                let d = document.getElementById('dialogoNuevo');
                let x = new bootstrap.Modal(d, {backdrop: true});
                x.show();
            },
            /**
             * Añadir al listado de categorías los datos de la categoría añadida
             */
            nuevaCat(categoria) {
                // añadir al inicio del listado
                this.categorias.unshift(categoria);

                // cerrar ventana modal de añadir canal
                let d = document.getElementById('dialogoNuevo');
                let m = bootstrap.Modal.getInstance(d);    
                m.hide();
            },
            /**
             * Se recibe de componente hijo orden de eliminar categoría.
             * Mostrar ventana modal de confirmación
             * @param int idCategoria identificador en base de datos de la categoría a eliminar
             */
            eliminar(idCategoria) {
                this.idCatEliminar = idCategoria; // guardar valor de categoría a eliminar
                // abrir ventana de confirmación
                let d = document.getElementById('conf');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },
            /**
             * Abrir ventana para modificar categoría
             */
            abrirModCat(categoria) {
                this.catMod = categoria; // datos de categoría a modificar
                // abrir ventana modal
                let d = document.getElementById('dialogoModificar');
                let x = new bootstrap.Modal(d, {backdrop: true});
                x.show();
            },
            /**
             * Actualizar datos de categoría modificada en vista Web
             */
            modCat(categoria) {
                //buscar la posición en el vector de la categoria modificada
                for(var i=0; i < this.categorias.length; i++)
                {
                    if (this.categorias[i].id == categoria.id)
                    {
                        this.categorias.splice(i, 1, categoria); // sustituir por nuevos valores
                        break;
                    } 
                }
                // cerrar ventana modal de modificar categoría
                let d = document.getElementById('dialogoModificar');
                let m = bootstrap.Modal.getInstance(d);    
                m.hide();
            },
            /**
             * Cambio de valor de visibilidad de categoría
             */
            cambioVisibilidad(categoria)
            {
                const parametros = {};

                // llamada a API de aplicación para modificar visibilidad de categoría
                axios.put('/api/categoria/cambiovisibilidad/' + categoria.id, parametros).then((respuesta) => 
                {
                    if (respuesta.data.error) // error en operación
                    {
                        alert(respuesta.data.error);
                    }
                    else
                    {
                        const categoriaR = respuesta.data;
                        //buscar la posición en el vector de la categoria modificada
                        for(var i=0; i < this.categorias.length; i++)
                        {
                            if (this.categorias[i].id == categoriaR.id)
                            {
                                this.categorias.splice(i, 1, categoriaR); // sustituir por nuevos valores
                                break;
                            } 
                        }
                    }
                });
            },
            /**
             * Se analiza la respuesta del usuario a mensaje de eliminación.
             * Si positivo enviar orden de eliminación a API de aplicación
             */
            respuestaEliminar(resp){
                // ocultar ventana de confiramción
                let d = document.getElementById('conf');
                let x = bootstrap.Modal.getInstance(d);
                x.hide();

                if (resp) // eliminar categoría
                {
                    axios.delete('/api/categoria/' + this.idCatEliminar).then((response) => 
                    {
                        //buscar la posición en el vector de la categoría a eliminar
                        for(var i=0; i < this.categorias.length; i++)
                        {
                            if (this.categorias[i].id == this.idCatEliminar) break;
                        }
                        // eliminar categoría de vector
                        this.categorias.splice(i, 1);
                    });
                }
            }
        }
    }
</script>