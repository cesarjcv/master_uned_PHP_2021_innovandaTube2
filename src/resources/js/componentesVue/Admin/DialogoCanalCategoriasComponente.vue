<template>
    <div class="modal fade" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="CanalCategorias" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar categor&iacute;a para Canal</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body selCat">
                    <table class="table table-hover">
                        <tbody>
                            <tr v-for="cat in categorias" :key="cat.id">
                                <td @click="categoriaSel(cat.id)" style="cursor:pointer">{{ cat.nombre }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <!--<button type="button" class="btn btn-success" @click="guardarCategoriasSel()" v-bind:disabled="trabajando">
                        <span v-if="trabajando" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Guardar
                    </button>-->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" v-bind:disabled="trabajando">Cerrar</button>
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
         * - valor lógico que indica que se está esperando repuesta del servidor
         */
        props:['identificador', 'categorias', 'trabajando'],

        methods:{
            /**
             * Categoría seleccionada
             * Enviar a componente padre categoría seleccionada
             * @param idCat identificador en base de datos de categoría
             */
            categoriaSel(idCat)
            {
                //console.log(idCat);
                this.$emit('seleccionCat', idCat);
            }
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
            },*/
            /**
             * desmarca todas las casillas de selección
             */
            /*limpiarSeleccion()
            {
                // buscar todas las entradas señaladas
                let lista = document.getElementById(this.identificador).querySelectorAll(".form-check-input:checked");
                for (let i=0; i < lista.length; i++)
                {
                    lista[i].checked = false; // desmarcar entrada
                }
            },*/
            /**
             * Marcar las categorías del video actual
             */
            /*marcarSelActual(cat)
            {
                let lista = document.getElementById(this.identificador).querySelectorAll("input.form-check-input"); // lista elementos HTMl de categorías
                // crear vector con id de categorías actuales
                let catNum = Array();
                cat.forEach(elemento => catNum.push(parseInt(elemento.idcategoria)));

                for (let i=0; i < lista.length; i++)
                {
                    if (catNum.indexOf(parseInt(lista[i].dataset.idcat)) == -1) lista[i].checked = false; // desmarcar entrada
                    else lista[i].checked = true; // desmarcar entrada
                }
            }*/
        },
    };
</script>