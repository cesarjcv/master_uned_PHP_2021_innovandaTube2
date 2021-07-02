<template>
    <div class="modal fade" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="VideoCategorias" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <!--<div class="modal-header">
                <h5 class="modal-title">Seleccionar categor&iacute;as de v&iacute;deo</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>-->
            <div class="modal-body">
                <div class="embed-responsive">
                <iframe width="480" height="360" :src="url" frameborder="0" class="embed-responsive-item">
                </iframe>
                </div>
            </div>
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-success" @click="guardarCategoriasSel()" v-bind:disabled="trabajando">
                    <span v-if="trabajando" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Guardar
                </button>-->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        /**
         * - Identificador para la ventana modal
         * - Listado de todas las categorías
         * - id de categorías de video actual
         * - valor lógico que indica que se está esperando repuesta del servidor
         */
        props:['identificador', 'idvideo'],
        data(){
            return {
                url: "", 
                //catVideos: [], // categorías del video
            }
        },
        mounted()
        {
            this.url = "https://www.youtube.com/embed/1w7OgIMMRc4";// + this.idvideo;
            console.log(this.idvideo);
            // ajustar saltos de línea
            //if (this.video.descripcion) this.des = this.video.descripcion.replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll("\n", "<br />");
            //console.log(this.categorias);
            // cargar listado de categorías del video
            /*axios.get('/api/video/categorias/' + this.video.id).then((response) => 
            {
                this.catVideos = response.data;
                //console.log(response);
                //console.log(this.catVideos);
            });*/
        },

        methods:{
            /**
             * Marcar las categorías del vídeo actual
             * @param int idCat identificadro de la categoría a comporbar
             * @return string|null
             */
            /*seleccionado(idcat)
            {
                // recorrer todas las categorías del vídeo
                for (var i=0; i < this.catActuales.length; i++) 
                    if (this.catActuales[i].idcategoria == idcat) // si coincide un valor, marcar
                    {
                        //console.log(idcat);
                        return "checked";
                    }
                return null;
            },*/
            /**
             * Enviar a componente padre listado de categorías seleccionadas para el vídeo
             */
            /*guardarCategoriasSel()
            {
                // buscar los elementos marcados
                let lista = document.getElementById(this.identificador).querySelectorAll(".form-check-input:checked");

                let salida = new Array(); // lista de id de categorías
                for (let i=0; i < lista.length; i++)
                {
                    salida.push(lista[i].dataset.idcat); // añadir a vector de salida
                }
                this.$emit('seleccionCat', salida); // enviar a componente padre datos de categorías seleccionadas
            }*/

        },
    };
</script>