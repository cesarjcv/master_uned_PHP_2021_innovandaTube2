<template>
    <div >
        <carrusel-componente v-for="listaCat in listados" :key="listaCat.id" :categoria="listaCat" @verVideo="verVideo">
        </carrusel-componente>
        <ver-video-componente :identificador="identificadorVentana" ref="videorep"></ver-video-componente>
    </div>
    
</template>


<script>
import VerVideoComponente from './VerVideoComponente.vue';

export default {
  components: { VerVideoComponente },

    data() {
        return {
            listados: [], // listado de categorías
            identificadorVentana: "ventanaVideo",
            videorep: null,
            numerobus: 0,
        }
    },
    mounted() {
        // obtener listado de categorías
        axios.put('api/categoria/convideo', {}/*parametros*/)
            .then((respuesta) => {
                this.listados = respuesta.data;
            });
        this.$root.$on('busqueda', this.buscar);
    },
    methods: {
        /**
         * Abrir ventana para reproducir vídeo
         */
        verVideo(video)
        {
            // establecer el vídeo a reproducir
            this.$refs.videorep.setVideo(video);
            // abrir ventana de reproducción de video
            let d = document.getElementById('ventanaVideo');
            this.$refs.videorep.ocultarInfo(); // ocultar cuadro de información
            this.$refs.videorep.ocultarCompartir(); // ocultar panel de compartir
            let x = new bootstrap.Modal(d, {backdrop: 'static'});
            x.show();
        },
        /**
         * Crear listado con resultado de búsqueda
         */
        buscar(valores)
        {
            this.listados.splice(0); // limpiar listado de categorías
            let listabus = {};
            listabus.id = this.numerobus--; // establecer un id para el listado, siempre un número negativo que no se repite
            listabus.nombre = "Búsqueda: \"" + valores.texto + "\""; // nombre para mostrar
            listabus.parametros = valores;
            this.listados.push(listabus);
        }
    }
}
</script>