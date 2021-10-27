<template>
    <div class="modal fade" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="CanalCategorias" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seleccionar categor&iacute;a para Canal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body selCat">
                    <table class="table table-hover">
                        <tbody>
                            <tr v-for="cat in categorias" :key="cat.id" >
                                <td @click="categoriaSel(cat.id)" style="cursor:pointer">{{ cat.nombre }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
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
                this.$emit('seleccionCat', idCat);
            }
        },
    };
</script>