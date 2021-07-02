<template>
    <div>
        <!--<div class='anadir'>
            <button class="btn btn-primary" @click="abrirNuevoCanal()">Añadir canal</button>
        </div>-->

        <dialogo-video-categorias-componente identificador="dialogoVideoCategorias" :categorias="categorias" ref="dialogoCat"
            :videoid="catVideoid"  v-on:seleccionCat="categoriasSeleccionadas"  :trabajando="selCatTrabajando">
            <!-- :catActuales="catCategorias"  -->
        </dialogo-video-categorias-componente>

        <dialogo-previsualizar-video-componente identificador="dialogoPrevisualizar" :idvideo="videoidPrev">
        </dialogo-previsualizar-video-componente>

        <!--v-show="nuevovisible" -->
        <!--<dialogo-confirmacion-componente id="conf" texto="¿Seguro que quiere eliminar este canal y todos sus videos asociados?"
            v-on:respuesta="respuestaEliminar"></dialogo-confirmacion-componente>-->

        <div class="container">
            <div class="row row-cols-auto">
                <entrada-video-componente v-for="video in videos" :key="video.id" :video="video" :categorias="categorias" 
                    v-on:selCategorias="seleccionCategorias"  v-on:preVideo="preVideo">
                    <!-- :catActuales="catCategorias" -->
                </entrada-video-componente>
            </div>
        </div>

        <nav aria-label="paginas">
        <ul class="pagination justify-content-center">
            <template v-for="numeroPagina in ultimaPagina" >
            <li class="page-item" :class="{active: paginaActual === numeroPagina}" :key="numeroPagina" >
                <a href="#" class="page-link" @click="establecerPagina(numeroPagina)" >{{ numeroPagina }}</a>
            </li>
            </template>
        </ul>
        </nav>
    </div>
</template>

<script>
import DialogoPrevisualizarVideoComponente from './DialogoPrevisualizarVideoComponente.vue';

    export default {
  components: { DialogoPrevisualizarVideoComponente },

        data(){
            return {
                videos: [], // listado de videos
                paginaActual: 1, // número de página actual
                ultimaPagina: 0,
                categorias: [], // listado de categorias
                catVideoid: 0, // id de video a cambiar categorías
                //catCategorias: [], // categorías actuales de video a modificar categorías
                selCatTrabajando: false, // se están guardnado las categorías seleccionadas en el servidor
                videoidPrev: "",
            }
        },
        mounted() {
            // cargar listado de categorías
            axios.get('/api/categoria').then((response) => 
            {
                this.categorias = response.data;
                //console.log(response);
                //console.log(this.categorias);
            });
            
            //console.log('Montado AdminCanales.')
            this.datosVideosPagina();

            
            
        },
        methods: {
            /**
            * cambiar página actual
            */
            establecerPagina(pagina){
                this.paginaActual = pagina;
                this.datosVideosPagina();
            },
            /**
             * Leer datos de videos de la página actual
             */
            datosVideosPagina()
            {
                // obtener listado de canales (paginado)
                axios.get('/api/video?page=' + this.paginaActual).then((response) => 
                {
                        this.videos = response.data.data;

                        this.paginaActual = response.data.current_page;
                        this.ultimaPagina = response.data.last_page;
                });
            },
             /**
             * Actualizar en base de datos e interfaz las nueva selección de categorías para el vídeo
             */
            categoriasSeleccionadas(listaid) {
                const parametros = {cat: listaid};
                this.selCatTrabajando = true;

                // llamada a API de aplicación para modificar visibilidad de categoría
                axios.put('/api/video/categorias/' + this.catVideoid, parametros).then((respuesta) => 
                {
                    if (respuesta.data.error) // error en operación
                    {
                        alert(respuesta.data.error);
                    }
                    else
                    {
                        // crear lista de objetos categorías
                        let lCat = new Array();
                        for (let i=0; i < listaid.length; i++)
                        {
                            lCat.push(new Object({idcategoria: listaid[i]}));
                        }
                        // buscar entrada de video y modificar su listado de categorías
                        for(let i=0; i < this.videos.length; i++)
                        {
                            if (this.videos[i].id == this.catVideoid)
                            {
                                this.videos[i].categorias = lCat; // sustituir por nuevos valores
                                break;
                            } 
                        }
                        // cerrar ventana modal
                        let d = document.getElementById('dialogoVideoCategorias');
                        let m = bootstrap.Modal.getInstance(d);    
                        m.hide();
                        /*const categoriaR = respuesta.data;
                        //buscar la posición en el vector de la categoria modificada
                        for(var i=0; i < this.categorias.length; i++)
                        {
                            if (this.categorias[i].id == categoriaR.id)
                            {
                                this.categorias.splice(i, 1, categoriaR); // sustituir por nuevos valores
                                break;
                            } 
                        }*/
                    }
                    this.selCatTrabajando = false;
                });
                /*if (Array.isArray(listaid))
                {
                    canal.forEach(element => {
                        this.canales.unshift(element);
                    });
                }
                else this.canales.unshift(canal);

                // cerrar ventana modal de añadir canal
                let d = document.getElementById('dialogoNuevo');
                let m = bootstrap.Modal.getInstance(d);    
                m.hide();*/
                //console.log(listaid);
            },
            /**
             * Se recibe de componente hijo orden de abrir ventana para selección de categorías.
             * @param int idVideo identificadro en base de datos del video
             * @param int[] categorias lista de categorías del vídeo
             */
            seleccionCategorias(idVideo, categorias) {
                //this.$refs.dialogoCat.limpiarSeleccion(); // limpiar las selecciones anteriores
                this.catVideoid = idVideo; // estbalecer el identificador del video a tratar
                //this.catCategorias = categorias.slice(0); // lista de categorías actuales del video
                //console.log(this.catVideoid);
                //console.log(this.catCategorias);
                this.$refs.dialogoCat.marcarSelActual(categorias);
                // abrir ventana de selección de categorías
                let d = document.getElementById('dialogoVideoCategorias');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },
            /**
             * Se recibe de componente hijo orden de abrir ventana para ver video.
             * @param string videoid identificador en youtube del video
             */
            preVideo(videoid) {
                console.log(videoid);
                this.videoidPrev = videoid;
                //this.catVideoid = idVideo;
                //this.catCategorias = categorias.slice(0);
                //console.log(categorias);
                //console.log(this.catCategorias);
                //this.canalEliminar = idCanal;
                // abrir ventana de confirmación
                let d = document.getElementById('dialogoPrevisualizar');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },
        }
    }
</script>