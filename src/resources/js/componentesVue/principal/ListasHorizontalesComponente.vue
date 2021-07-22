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
            //listas: [1, 2],
            listados: [],
            identificadorVentana: "ventanaVideo",
            videorep: null,
        }
    },
    mounted() {

        //const parametros = {listas: this.listas};
        // obtener listado de categorías
        axios.put('api/categoria/convideo', {}/*parametros*/)
            .then((respuesta) => {
                this.listados = respuesta.data;
                //console.log(this.listados);
                /*console.log(respuesta.data);*/
            });
    },
    methods: {
        verVideo(video)
        {
            console.log("lista");
            this.$refs.videorep.setVideo(video);
            // abrir ventana de reproducción de video
            let d = document.getElementById('ventanaVideo');
            this.$refs.videorep.ocultarInfo();
            let x = new bootstrap.Modal(d, {backdrop: 'static'});
            x.show();
        }
    }
}
</script>