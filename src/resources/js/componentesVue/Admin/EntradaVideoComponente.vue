<template>
    <div class="col columnavideo">
        <div class="card tarjetavideo" :class="[video.visible ? '' : 'novisible' ]">
            <img :src="video.imagen" class="card-img-top" :alt="video.titulo">
            <div class="card-body overflow-auto" style='height:150px'>
                <h6 class="card-title"><strong>{{ video.titulo }}</strong></h6>
                <p class="card-text" v-html="this.des"></p>
            </div>
            <div class="card-footer">
                <small class="text-muted" style="padding-bottom: 15px; display: inline-block;">{{ preFecha(video.fecha) }}</small>
                <!--<button type="button" class="btn btn-outline-primary float-end ms-2" @click="selCategorias()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmarks" viewBox="0 0 16 16">
                    <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L7 13.101l-4.223 2.815A.5.5 0 0 1 2 15.5V4zm2-1a1 1 0 0 0-1 1v10.566l3.723-2.482a.5.5 0 0 1 .554 0L11 14.566V4a1 1 0 0 0-1-1H4z"/>
                    <path d="M4.268 1H12a1 1 0 0 1 1 1v11.768l.223.148A.5.5 0 0 0 14 13.5V2a2 2 0 0 0-2-2H6a2 2 0 0 0-1.732 1z"/>
                    </svg>
                </button>-->
                <button v-if="administrador" type="button" class="btn btn-outline-danger float-end ms-2" @click="eliminar()">
                    <i class="bi bi-trash"></i>
                </button>
                <button type="button" class="btn btn-outline-primary float-end ms-2" @click="preVideo()">
                    <i class="bi bi-play-circle"></i>
                </button>
                <button type="button" class="btn btn-outline-primary float-end" @click="cambiarVisibilidad()">
                    <!--<i class="bi bi-eye-fill"></i>-->
                    <i class="bi" :class="[video.visible ? visibleClass : noVisibleClass]"></i>
                </button>
                <div v-for="cat in video.categorias" :key="cat.idcategoria" style='margin-top:10px'>
                    <button class="categoriaVideo">
                    {{ nombreCategoria(cat.idcategoria) }}
                    </button>
                </div>
                <div v-for="catcan in video.canalcategorias" :key="catcan.idcategoria + 'c'" style='margin-top:10px'>
                    <button class="categoriaCanal">
                    {{ nombreCategoria(catcan.idcategoria) }}
                    </button>
                </div>
            </div>
        </div>
        <div class="card tarjetavideo" :class="[video.visible ? '' : 'novisible' ]">
            <div class="card-body">
                <h5 class="card-title">Categorías</h5>
                <div class="listaCat">
                    <div class="form-check form-switch" v-for="cat in categorias" :key="cat.id"> 
                        <input class="form-check-input" type="checkbox" v-model='catVideos' :value="cat.id" :id="'seleccion' + cat.id" :data-idcat="cat.id" @change="actualizarCat($event)">
                        <!-- :checked="seleccionado(cat.id)" -->
                        <label class="form-check-label" :for="'seleccion' + cat.id" >{{ cat.nombre }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        /**
         * Datos del vídeo a mostrar
         * Listado de categorías
         * Índica si el usuario actual es administrador
         */
        props: ['video', 'categorias', 'administrador'],
        data(){
            return {
                des: "", // descripción con ajuste de saltos de línea
                catVideos: [], // categorías del video
                visibleClass: "bi-eye-slash-fill",
                noVisibleClass: "bi-eye-fill",
            }
        },
        mounted()
        {
            // ajustar saltos de línea
            if (this.video.descripcion) this.des = this.video.descripcion.replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll("\n", "<br />");
            // cargar vector de categorías actuales
            this.video.categorias.forEach(element => {
                this.catVideos.push(element.idcategoria);
            });
         },

        methods:{
            /**
             * Cambiar fromato de fecha para mostrar
             * @param string f fecha en formato AAAA-MM-DD HH:mm:ss
             * @return string fecha en formato DD/MM/AAAA
             */
            preFecha(f)
            {
                let t = f.substring(0,10).split('-');
                return t[2] + "/" + t[1] + "/" + t[0];
            },
            /**
             * Nombre de categoiría por ID
             * @param int idcategoria identificador de categoría
             * @return string nombre de categoría
             */
            nombreCategoria(idcategoria)
            {
                for(var i=0; i < this.categorias.length; i++)
                {
                    if (this.categorias[i].id == idcategoria) return this.categorias[i].nombre;
                }
                return null;
            },
            /**
             * enviar a componente padre un mensaje de abrir ventana para selección de categorías de video
             */
            selCategorias()
            {
                this.$emit('selCategorias', this.video.id, this.video.categorias);
            },
            /**
             * enviar a componente padre un mensaje de abrir ventana para previsualizar video
             */
            preVideo()
            {
                this.$emit('preVideo', this.video);
            },
            /**
             * Cambiar la visibilidad del vídeo
             */
            cambiarVisibilidad()
            {
                const parametros = {idvideo: this.video.id, visible: (this.video.visible == 0 ? 1 : 0)};
                // llamada a API de aplicación para cambiar visibilidad
                axios.put('/api/video/visibilidad', parametros).then((respuesta) => 
                {
                    if (respuesta.data.error) // error en operación
                    {
                        alert(respuesta.data.error);
                    }
                    else
                    {
                        this.video.visible = parametros.visible;
                    }
                });
            },
            /**
             * Actualizar categorías.
             * Se pulso sobre un checkbox
             * Añadir o eliminar la categoría para el video
             */
            actualizarCat(evento)
            {
                const parametros = {idvideo: this.video.id, categoria: evento.target.value};
                if (evento.target.checked) // se selecciona categoría
                {
                    axios.put('/api/video/categoria/insertar', parametros).then((respuesta) => 
                    {
                        //actualizar listado de objeto vídeo
                        this.video.categorias.push({idcategoria: parseInt(evento.target.value)});
                    });
                    
                }
                else // se deselecciona categoría
                {
                    
                    axios.put('/api/video/categoria/eliminar', parametros).then((respuesta) => 
                    {
                        // buscar identificador de categoría para eliminar del listado
                        for( var i = 0; i < this.video.categorias.length; i++)
                        { 
                                    
                            if ( this.video.categorias[i].idcategoria === parseInt(evento.target.value)) 
                            { 
                                this.video.categorias.splice(i, 1); 
                                break; 
                            }
                        }
                    });
                    
                }
            },
            /**
             * enviar a componente padre un mensaje de eliminación de este vídeo
             */
            eliminar()
            {
                this.$emit('eliminar', this.video.id);
            },
        }
    }
</script>