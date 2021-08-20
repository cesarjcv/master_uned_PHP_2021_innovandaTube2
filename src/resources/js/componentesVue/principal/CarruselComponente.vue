<template>
    <div >
         <div class="text-light tituloCategoria m-0 p-2 pl-5 " ref="titulo">
            {{categoria.nombre}} : {{videos.length}} vídeos
            <i class="bi bi-calendar3" data-bs-toggle="tooltip" data-bs-placement="top" title="Ordenar por fecha de publicación" @click="ordenFecha()"></i>
            <i class="bi bi-hand-thumbs-up" data-bs-toggle="tooltip" data-bs-placement="top" title="Ordenar por mayor número de votos positivos" @click="ordenVotos()"></i>
            <i class="bi bi-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Ordenar por número de reproducciones" @click="ordenRepro()"></i>
        </div>

        <flecha-componente v-for="flecha in flechas" :key="flecha.id" :flecha="flecha" @atras="atras" @adelante="adelante">
        </flecha-componente>

        <div class="barravideos overflow-hidden">
            <ul class="w-100 d-flex" v-bind:style="{ transform: 'translate3d('+ posicion.x + 'px, 0px, 0px)' }">
                <video-carrusel-componente v-for="video in videos" :key="video.id" :video="video" @verVideo="verVideo">
                </video-carrusel-componente>
            </ul>
        </div>
    </div>
</template>


<script>

export default {

    props: ['categoria'],

    data() {

        return {
            posicion: {
                x: 0, page: 0
            },
            videos: [],
            flechas: [
                { // izquierda
                    id: "izq",
                    sentido: "i",
                    evento: 'atras',
                    visible: true
                },
                { // derecha
                    id: "der",
                    sentido:"d",
                    evento: 'adelante',
                    visible: true,
                }
            ],
            anchoVideo: 308, // ancho en pixeles de un video en carrusel
            nv: 0, // número de videos que caben en una línea
            limite: 0, // límite de movimiento a la derecha
        }
    },
    created() {
        window.addEventListener("resize", this.calculoValores); // evento de cambio tamaño de página
    },
    destroyed() {
        window.removeEventListener("resize", this.calculoValores);
    },
    mounted() {
        //console.log('carrusel mounted.');
        if (this.categoria.id <= 0)
        {
            //console.log(this.categoria.parametros);
            axios.put('api/video/buscar', this.categoria.parametros).then((respuesta) => 
            {
                //this.listados = respuesta.data;
                //console.log(this.listados);
                //console.log(respuesta.data);
                this.videos = respuesta.data;

                this.calculoValores(); // calcular dimensiones
                this.vistaFlechas(); // visibilidad flechas
            });
        }
        else
        {
            axios.get('api/categoria/' + this.categoria.id + '/videos/').then((respuesta) => {
                this.videos = respuesta.data;
                //console.log(this.videos);

                this.calculoValores(); // calcular dimensiones
                this.vistaFlechas(); // visibilidad flechas
            });
        }
        

        // activar viñetas de ayuda para botones del título
        var tooltipTriggerList = [].slice.call(this.$refs.titulo.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {return new bootstrap.Tooltip(tooltipTriggerEl)});
        //console.log(tooltipList);
    },
    methods: {
        /**
         * Calculo de valores dimensionales de la pantalla
         */
        calculoValores() {
            // calcular número de videos que caben a lo ancho
            this.nv = Math.floor((document.documentElement.clientWidth - 40)/this.anchoVideo);
            // cálcular el límite de desplazamiento según número de videos
            this.limite = -(this.anchoVideo * (this.videos.length - this.nv));
            //console.log(this.nv + " " + this.limite);
        },
        /**
         * avanzar hacia la izquierda en el carrusel de vídeos
         */
        atras()
        {
            // calcular nueva posición
            this.posicion.x += this.nv * this.anchoVideo;
            if (this.posicion.x > 0) this.posicion.x = 0; //comporbar si se supera el límite
 
            this.vistaFlechas();
        },
        /**
         * avanzar hacia la derecha en el carrusel de vídeos
         */
        adelante()
        {
            // calcular nueva posición
            this.posicion.x -= this.nv * this.anchoVideo;
            if (this.posicion.x <  this.limite) this.posicion.x = this.limite; //comporbar si se supera el límite

            this.vistaFlechas();
        },
        /**
         * Establecer si las flechas de movimiento tienen que estar visibles u ocultas.
         */
        vistaFlechas()
        {
            // Flecha atras
            if (this.posicion.x == 0)  // si estamos en la posición inicial no mostrar flecha izquierda
            {
                this.flechas[0].visible = false;
            } 
            else 
            {
                this.flechas[0].visible = true;
            }

            // Flecha siguiente
            if (this.posicion.x == this.limite || this.limite >= 0)  // si estamos en la posición exterma a la derecha o no se alcanza ancho de pantalla no mostrar
            {
                this.flechas[1].visible = false;
            } 
            else 
            {
                this.flechas[1].visible = true;
            }
        },
        verVideo(video)
        {
            //console.log("carrusel");
            this.$emit('verVideo', video); // enviar a componente padre datos de video a reproducir
        },
        /**
         * Ordena por fecha de publicación los vídeos, primero los más recientes
         */
        ordenFecha()
        {
            //console.log(this.videos);
            this.videos.sort(function comparar(elemento1, elemento2)
            {  
                if (elemento1.fecha > elemento2.fecha) return -1;
                else if (elemento1.fecha < elemento2.fecha) return 1;
                else return 0;
            });
            //console.log(this.videos);
        },
        /**
         * Ordena los vídeos por mayor número de votos positivos
         */
        ordenVotos()
        {
            this.videos.sort(function comparar(elemento1, elemento2)
            {  
                if (elemento1.estgusta > elemento2.estgusta) return -1;
                else if (elemento1.estgusta < elemento2.estgusta) return 1;
                else return 0;
            });
        },
        /**
         * Ordena los vídeo por número de reproducciones, primero el más reproducido
         */
        ordenRepro()
        {
            this.videos.sort(function comparar(elemento1, elemento2)
            {  
                if (elemento1.estrep > elemento2.estrep) return -1;
                else if (elemento1.estrep < elemento2.estrep) return 1;
                else return 0;
            });
        }
    }
}
</script>