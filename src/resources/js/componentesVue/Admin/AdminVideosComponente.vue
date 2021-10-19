<template>
    <div>

        <!--<dialogo-video-categorias-componente identificador="dialogoVideoCategorias" :categorias="categorias" ref="dialogoCat"
            :videoid="catVideoid"  v-on:seleccionCat="categoriasSeleccionadas"  :trabajando="selCatTrabajando">
        </dialogo-video-categorias-componente>-->

        <dialogo-previsualizar-video-componente identificador="dialogoPrevisualizar" ref="prevideo">
        </dialogo-previsualizar-video-componente>

        <dialogo-confirmacion-componente id="conf" texto="¿Seguro que quiere eliminar este vídeo?"
            v-on:respuesta="respuestaEliminar"></dialogo-confirmacion-componente>

        <div class="mb-3 row">
            <label for="fcanal" class="col-sm-1 col-form-label tituloFiltro">Canal</label>
            <div class="col-sm-3">
                <select class="form-select" aria-label="canal" id="fcanal" @change="cambioFiltro" v-model="filtroCanal">
                    <option value="0" selected>Todos</option>
                    <option v-for="canal in canales" :key="canal.id" :value="canal.id">
                        {{ canal.nombre }}
                    </option>
                </select>
            </div>
            <label for="fcat" class="col-sm-1 col-form-label tituloFiltro">Categoría</label>
            <div class="col-sm-3">
                <select class="form-select" aria-label="categoría" id="fcat" @change="cambioFiltro" v-model="filtroCategoria">
                    <option value="0" selected>Todas</option>
                    <option value="-1">Sin categoría</option>
                    <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                        {{ cat.nombre }}
                    </option>
                </select>
            </div>
            <label for="ftexto" class="col-sm-1 col-form-label tituloFiltro">Texto filtro</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="ftexto" value="" @keyup.enter="cambioFiltro" v-model="filtroTexto">
            </div>
        </div>
        <div class="container-fluid" name="inicio">
            <div class="row row-cols-auto">
                <entrada-video-componente v-for="video in videos" :key="video.id" :video="video" :categorias="categorias" 
                      v-on:preVideo="preVideo" :administrador="administrador" v-on:eliminar="eliminar">
                 </entrada-video-componente>
                 <!-- v-on:selCategorias="seleccionCategorias" -->
            </div>
        </div>

        <nav aria-label="paginas">
        <ul class="pagination justify-content-center">
            <li class="page-item" v-bind:class="{ disabled: paginaActual == 1 }">
                <a href="#" class="page-link" @click="establecerPagina(1)">
                    <i  class="bi bi-chevron-bar-left"></i>
                </a>
            </li>
            <li class="page-item" v-bind:class="{ disabled: paginaActual == 1 }">
                <a href="#" class="page-link" @click="establecerPagina(paginaActual - 1)">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
            <li style="padding: 5px 12px;">Página 
                <select @change="cambiarPagina">
                    <option v-for="numeroPagina in ultimaPagina" :key="numeroPagina" :value="numeroPagina" :selected="numeroPagina == paginaActual ? 'selected' : null">
                        {{ numeroPagina }}
                    </option>
                </select>
            </li>
            <li class="page-item" v-bind:class="{ disabled: paginaActual == ultimaPagina}">
                <a href="#" class="page-link" @click="establecerPagina(paginaActual + 1)">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <li class="page-item" v-bind:class="{ disabled: paginaActual == ultimaPagina}">
                <a href="#" class="page-link" @click="establecerPagina(ultimaPagina)">
                    <i class="bi bi-chevron-bar-right"></i>
                </a>
            </li>
        </ul>
        </nav>
    </div>
</template>

<script>
import DialogoPrevisualizarVideoComponente from './DialogoPrevisualizarVideoComponente.vue';
import DialogoConfirmacionComponente from './DialogoConfirmacionComponente.vue';

    export default {
  components: { DialogoPrevisualizarVideoComponente, DialogoConfirmacionComponente },

        /**
         * Indica si el usuario actual es administrador
         */
        props:['administrador'],

        data(){
            return {
                videos: [], // listado de videos
                paginaActual: 1, // número de página actual
                ultimaPagina: 0,
                categorias: [], // listado de categorias
                canales: [], // listado de canales
                catVideoid: 0, // id de video a cambiar categorías
                selCatTrabajando: false, // se están guardnado las categorías seleccionadas en el servidor
                videoidPrev: "",
                filtroCategoria: 0,
                filtroCanal: 0,
                filtroTexto: "",
                videoEliminar: 0, // identificador del vídeo a eliminar
            }
        },
        mounted() {
            // cargar listado de categorías
            axios.get('/api/categoria').then((response) => 
            {
                this.categorias = response.data;
            });

            // cargar listado de canales
            axios.get('/api/canal').then((response) => 
            {
                this.canales = response.data;
            });
            
            //cargar vídeos de la página actual
            this.datosVideosPagina();
        },
        methods: {
            /**
            * cambiar página actual
            * @param pagina número de página nueva
            */
            establecerPagina(pagina){
                this.paginaActual = pagina;
                //cargar vídeos de la nueva página actual
                this.datosVideosPagina();

                //desplazar hasta el inicio de la página
                window.scrollTo(0, 0);
            },
            /**
             * Respuesta al evento de cmabio de página en selector
             * @param evento datos del evento
             */
            cambiarPagina(evento){
                // cambiar página actual
                this.establecerPagina(evento.target.value);
            },
            /**
             * Leer datos de videos de la página actual
             */
            datosVideosPagina()
            {
                // obtener listado de canales (paginado)
                axios.get('/api/video?page=' + this.paginaActual + '&fcanal=' + this.filtroCanal + '&fcat=' + this.filtroCategoria + '&ftexto=' + 
                escape(this.filtroTexto)).then((response) => 
                {
                        // datos recabados de la página actual
                        this.videos = response.data.data;

                        // datos de paginación
                        this.paginaActual = response.data.current_page;
                        this.ultimaPagina = response.data.last_page;

                        // comporbar si la lista está vacía (es la última página y quedo sin elementos)
                        if (this.videos.length == 0)
                        {
                            // si no es la primera página retrocedemos una
                            if (this.paginaActual > 1)
                            {
                                this.paginaActual--;
                                this.datosVideosPagina();
                            } 
                        }
                });
            },
            /**
             * Se recibe de componente hijo orden de eliminar vídeo.
             * Mostrar ventana modal de confirmación
             * @param int idVideo identificador en base de datos del vídeo a eliminar
             */
            eliminar(idVideo) {
                this.videoEliminar = idVideo;
                // abrir ventana de confirmación
                let d = document.getElementById('conf');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
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

                if (resp) // eliminar vídeo
                {
                    //console.log(this.videoEliminar);
                    axios.delete('/api/video/' + this.videoEliminar).then((response) => 
                    {
                        /*//buscar la posición en el vector del canal a eliminar
                        for(var i=0; i < this.canales.length; i++)
                        {
                            if (this.canales[i].id == this.canalEliminar) break;
                        }
                        // eliminar canal de vector
                        this.canales.splice(i, 1);*/
                        // actualizar lista de vídeos visualizados
                        this.datosVideosPagina();
                        
                    });
                }
            },
             /**
             * Actualizar en base de datos e interfaz las nueva selección de categorías para el vídeo
             */
            /*categoriasSeleccionadas(listaid) {
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
                    }
                    this.selCatTrabajando = false;
                });
            },*/
            /**
             * Se recibe de componente hijo orden de abrir ventana para selección de categorías.
             * @param int idVideo identificadro en base de datos del video
             * @param int[] categorias lista de categorías del vídeo
             */
            /*seleccionCategorias(idVideo, categorias) {
                this.catVideoid = idVideo; // estbalecer el identificador del video a tratar

                this.$refs.dialogoCat.marcarSelActual(categorias); // marcar en ventana de diálogo las categorías del vídeo actual

                // abrir ventana de selección de categorías
                let d = document.getElementById('dialogoVideoCategorias');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },*/
            /**
             * Se recibe de componente hijo orden de abrir ventana para ver video.
             * @param string video datos del video
             */
            preVideo(video) {
                // establecer video a reproducir
                this.$refs.prevideo.setVideo(video);

                // abrir ventana
                let d = document.getElementById('dialogoPrevisualizar');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },
            /**
             * Cambio en uno de los select de filtro, canal o categoría
             * actualizar listado de vídeo con nuevos valores
             */
            cambioFiltro()
            {
                // la página actual se cambia a la primera
                this.paginaActual = 1;
                
                this.datosVideosPagina();
            }
        }
    }
</script>