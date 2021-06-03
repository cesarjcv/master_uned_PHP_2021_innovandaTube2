<template>
    <div class="modal fade" :id="id" tabindex="-1" role="dialog" aria-labelledby="Confirmar" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar</h5>
            </div>
            <div class="modal-body">
                <span>{{ texto }}</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" @click="respSi()" >SI</button>
                <button type="button" class="btn btn-secondary" @click="respNo()">NO</button>
            </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {

        /**
         * texto: texto a mostrar
         * id: identificador de ventana modal
         */
        props:['texto', 'id'],

        methods:{
            /**
             * al pulsar botón SI
             */ 
            respSi()
            {
                this.$emit('respuesta', true); // enviar mensaje a componente padre
            },
            /**
             * al pulsar botón NO
             */
            respNo()
            {
                this.$emit('respuesta', false); // enviar mensaje a componente padre
            },
        },
    };
</script>