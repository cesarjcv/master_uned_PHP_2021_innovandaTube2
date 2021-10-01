<template>
    <form class="d-flex" action=''>
        
        <input class="form-control form-control-sm me-2" type="search" placeholder="Buscar" aria-label="Buscar" @keydown.enter.prevent="entradaTexto" v-model="texto">
        <button class="btn " @click.prevent="pulsar()"><img src='img/lupa.png' alt='buscar'/></button>
    </form>
</template>

<script>
    export default {
        /**
         * - Identificador para la ventana de búsqueda
         */
        props:['vbus'],
        data(){
            return {
                texto : ""
            }
        },
        mounted() {
        },
        methods: {
            /**
             * Se pulsa sobre el icono de la lupa.
             * Se abre ventana para búsqueda avanzada.
             */
            pulsar()
            {
                // abrir ventana de búsqueda
                let d = document.getElementById(this.vbus);
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },
            /**
             * Evento de pulsación de tecla ENTER en el cuadro de búsqeuda
             */
            entradaTexto(event)
            {
                if (this.texto.trim().length > 0) // comprobar si hay texto en el cuadro
                {
                    let argumentos = {texto: this.texto.trim(), titulo: true, des: true, fini: "", ffin: ""};
                    this.$root.$emit('busqueda', argumentos); // enviar a componente padre mensaje con datos de búsqeuda
                }
            }
        }
    }
</script>