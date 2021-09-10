<template>
    <div>
        <dialogo-canal-categorias-componente identificador="dialogoCanalCategorias"  ref="dialogoCat"
            :canalid="catCanalid"  :categorias="categorias" v-on:seleccionCat="categoriaSeleccionada" :trabajando="selCatTrabajando">
        </dialogo-canal-categorias-componente>
        <!--     -->

        <div class='anadir'>
            <button class="btn btn-primary" @click="abrirNuevoCanal()">Añadir canal</button>
            <!-- data-bs-toggle="modal" data-bs-target="#dialogoNuevo"-->
        </div>

        <dialogo-nuevo-canal-componente identificador="dialogoNuevo" ref="tollo"  v-on:nuevoCanal="nuevoCanal" >

        </dialogo-nuevo-canal-componente>
        <!--v-show="nuevovisible" -->
        <dialogo-confirmacion-componente id="conf" texto="¿Seguro que quiere eliminar este canal y todos sus videos asociados?"
            v-on:respuesta="respuestaEliminar"></dialogo-confirmacion-componente>

        <div class="container-fluid">
            <div class="row row-cols-auto">
                <entrada-canal-componente v-for="canal in canales" :key="canal.id" :canal="canal" v-on:eliminar="eliminar" 
                    v-on:selCategoria="seleccionCategoria" :categorias="categorias" >
                </entrada-canal-componente>
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
                canales: [], // listado de canales
                //nuevovisible:false,
                canalEliminar: null, // id de canal a eliminar
                catCanalid: 0, // id de canal a cambiar categorías
                categorias: [], // listado de categorias
                selCatTrabajando: false, // se están guardnado las categorías seleccionadas en el servidor
            }
        },
        mounted() {
            //console.log('Montado AdminCanales.')
            // obtener listado de canales
            axios.get('/api/canal').then((response) => 
            {
                    this.canales = response.data;
            });

            // cargar listado de categorías
            axios.get('/api/categoria').then((response) => 
            {
                this.categorias = response.data;
            });

        },
        methods: {
            /**
             * Abrir ventana para nuevo canal
             */
            abrirNuevoCanal() {
                // abrir ventana modal
                let d = document.getElementById('dialogoNuevo');
                let x = new bootstrap.Modal(d, {backdrop: true});
                x.show();
            },
            /**
             * Añadir al listado de canales los datos de los canales añadidos
             */
            nuevoCanal(canal) {
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
            },
            /**
             * Se recibe de componente hijo orden de eliminar canal.
             * Mostrar ventana modal de confirmación
             * @param int idCanal identificadro en base de datos del canal a eliminar
             */
            eliminar(idCanal) {
                this.canalEliminar = idCanal;
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
            },
            /**
             * Se recibe de componente hijo orden de abrir ventana para selección de categorías.
             * @param int idVideo identificadro en base de datos del canal
             * @param int[] categorias lista de categorías del canal
             */
            seleccionCategoria(idCanal/*, categorias*/) {
                this.catCanalid = idCanal; // estbalecer el identificador del video a tratar

                //this.$refs.dialogoCat.marcarSelActual(categorias); // marcar en ventana de diálogo las categorías del vídeo actual

                // abrir ventana de selección de categorías
                let d = document.getElementById('dialogoCanalCategorias');
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },
            /**
             * Actualizar en base de datos e interfaz las nueva selección de categorías para el vídeo
             */
            categoriaSeleccionada(catid) {
                const parametros = {cat: catid};
                this.selCatTrabajando = true;
                //console.log(catid);

                // llamada a API de aplicación para asignar categoría
                axios.put('/api/canal/categorias/' + this.catCanalid, parametros).then((respuesta) => 
                {
                    if (respuesta.data.error) // error en operación
                    {
                        alert(respuesta.data.error);
                    }
                    else
                    {
                        // buscar entrada de video y modificar su listado de categorías
                        for(let i=0; i < this.canales.length; i++)
                        {
                            if (this.canales[i].id == this.catCanalid)
                            {
                                this.canales[i].categorias.push({idcategoria: catid}); // añadir nueva categoría
                                //console.log(this.canales[i].categorias);
                                break;
                            } 
                        }
                        // cerrar ventana modal
                        let d = document.getElementById('dialogoCanalCategorias');
                        let m = bootstrap.Modal.getInstance(d);    
                        m.hide();
                    }
                    this.selCatTrabajando = false;
                });
            },
        }
    }
</script>