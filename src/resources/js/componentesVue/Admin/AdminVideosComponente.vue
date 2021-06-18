<template>
    <div>
        <!--<div class='anadir'>
            <button class="btn btn-primary" @click="abrirNuevoCanal()">Añadir canal</button>
        </div>-->

        <!--<dialogo-nuevo-canal-componente identificador="dialogoNuevo" ref="tollo"  v-on:nuevoCanal="nuevoCanal" ></dialogo-nuevo-canal-componente>-->
        <!--v-show="nuevovisible" -->
        <!--<dialogo-confirmacion-componente id="conf" texto="¿Seguro que quiere eliminar este canal y todos sus videos asociados?"
            v-on:respuesta="respuestaEliminar"></dialogo-confirmacion-componente>-->

        <div class="container">
            <div class="row row-cols-auto">
                <entrada-video-componente v-for="video in videos" :key="video.id" :video="video" :categorias="categorias">
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

    export default {

        data(){
            return {
                videos: [], // listado de videos
                paginaActual: 1, // número de página actual
                ultimaPagina: 0,
                categorias: [], // listado de categorias
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
            }
            /**
             * Abrir ventana para nuevo canal
             */
            /*abrirNuevoCanal() {
                // abrir ventana modal
                let d = document.getElementById('dialogoNuevo');
                let x = new bootstrap.Modal(d, {backdrop: true});
                x.show();
            },*/
            /**
             * Añadir al listado de canales los datos de los canales añadidos
             */
            /*nuevoCanal(canal) {
                if (Array.isArray(canal))
                {
                    canal.forEach(element => {
                        this.canales.unshift(element);
                    });
                }
                else this.canales.unshift(canal);

                // cerrar ventana modal de añadir canal
                let d = document.getElementById('dialogoNuevo');
                let m = bootstrap.Modal.getInstance(d);    
                m.hide();
            },*/
            /**
             * Se recibe de componente hijo orden de eliminar canal.
             * Mostrar ventana modal de confirmación
             * @param int idCanal identificadro en base de datos del canal a eliminar
             */
            /*eliminar(idCanal) {
                this.canalEliminar = idCanal;
                // abrir ventana de confirmación
                let d = document.getElementById('conf');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },*/
            /**
             * Se analiza la respuesta del usuario a mensaje de eliminación.
             * Si positivo enviar orden de eliminación a API de aplicación
             */
            /*respuestaEliminar(resp){
                // ocultar ventana de confiramción
                let d = document.getElementById('conf');
                let x = bootstrap.Modal.getInstance(d);
                x.hide();

                if (resp) // eliminar canal
                {
                    axios.delete('/api/canal/' + this.canalEliminar).then((response) => 
                    {
                        //buscar la posición en el vector del canal a eliminar
                        for(var i=0; i < this.canales.length; i++)
                        {
                            if (this.canales[i].id == this.canalEliminar) break;
                        }
                        // eliminar canal de vector
                        this.canales.splice(i, 1);
                    });
                }
            }*/
        }
    }
</script>