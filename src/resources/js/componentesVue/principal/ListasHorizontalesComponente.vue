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
            numerobus: 0,
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
        this.$root.$on('busqueda', this.buscar);
    },
    methods: {
        verVideo(video)
        {
            //console.log("lista");
            this.$refs.videorep.setVideo(video);
            // abrir ventana de reproducción de video
            let d = document.getElementById('ventanaVideo');
            this.$refs.videorep.ocultarInfo();
            this.$refs.videorep.ocultarCompartir();
            let x = new bootstrap.Modal(d, {backdrop: 'static'});
            x.show();
        },
        buscar(valores)
        {
            //alert("hola");
            //console.log(valores);
            this.listados.splice(0);
            let listabus = {};
            listabus.id = this.numerobus--;
            listabus.nombre = "Búsqueda: \"" + valores.texto + "\"";
            listabus.parametros = valores;
            this.listados.push(listabus);
            //console.log(this.listados);
            /*axios.put('api/video/buscar', valores).then((respuesta) => 
            {
                //this.listados = respuesta.data;
                //console.log(this.listados);
                console.log(respuesta.data);
            });*/
        }
    }
}
</script>