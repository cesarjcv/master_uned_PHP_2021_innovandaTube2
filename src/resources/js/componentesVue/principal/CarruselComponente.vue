<template>
    <div >
         <div class="text-light tituloCategoria m-0 p-2 pl-5 " ref="titulo">
            {{categoria.nombre}} : {{totalvideos}} vídeos
            <i class="bi bi-calendar3" data-bs-toggle="tooltip" data-bs-placement="top" title="Ordenar por fecha de publicación" @click="ordenFecha()"></i>
            <i class="bi bi-hand-thumbs-up" data-bs-toggle="tooltip" data-bs-placement="top" title="Ordenar por mayor número de votos positivos" @click="ordenVotos()"></i>
            <i class="bi bi-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Ordenar por número de reproducciones" @click="ordenRepro()"></i>
            <i class="bi bi-star" data-bs-toggle="tooltip" data-bs-placement="top" title="Ordenar por índice de fiabilidad" @click="ordenFiabilidad()"></i>
        </div>

        <flecha-componente v-for="flecha in flechas" :key="flecha.id" :flecha="flecha" @atras="atras" @adelante="adelante">
        </flecha-componente>

        <div class="barravideos overflow-hidden">
            <ul class="w-100 d-flex" v-bind:style="{ transform: 'translate3d('+ posicion.x + 'px, 0px, 0px)' }">
                <video-carrusel-componente v-for="video in videosmostrados" :key="video.id" :video="video" @verVideo="verVideo">
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
            videosmostrados: [],
            posactual: 0, // posición actual del cursor de carrusel
            posmax: 0, // posición máxima alcanzada por cursor
            totalvideos: 0, // número total de vídeos
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
        if (this.categoria.id <= 0) // se trata de una búsqeuda
        {
            //console.log(this.categoria.parametros);
            axios.put('api/video/buscar', this.categoria.parametros).then((respuesta) => 
            {
                this.videos = respuesta.data;
                this.totalvideos = this.videos.length;

                this.calculoValores(); // calcular dimensiones
                this.vistaFlechas(); // visibilidad flechas

                // calcular los primeros vídeos a listar
                this.cargaPrimeros();
            });
        }
        else
        {
            axios.get('api/categoria/' + this.categoria.id + '/videos/').then((respuesta) => {
                this.videos = respuesta.data;
                this.totalvideos = this.videos.length;

                this.calculoValores(); // calcular dimensiones
                this.vistaFlechas(); // visibilidad flechas

                // calcular los primeros vídeos a listar
                this.cargaPrimeros();
            });
        }

        // activar viñetas de ayuda para botones del título
        var tooltipTriggerList = [].slice.call(this.$refs.titulo.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {return new bootstrap.Tooltip(tooltipTriggerEl)});
    },
    methods: {
        /**
         * Calcula y muestra los primeros vídeos que hay que mostar
         */
        cargaPrimeros()
        {
            let aux = this.videos.splice(0, 14); // carga de 14 vídeos incialmente
            this.videosmostrados = this.videosmostrados.concat(aux); // pasar el primer grupo a visulaizar
            this.posactual=1; this.posmax = 1; // iniciar contadores de posición
        },
        /**
         * Calculo de valores dimensionales de la pantalla
         */
        calculoValores() {
            // calcular número de videos que caben a lo ancho
            this.nv = Math.floor((document.documentElement.clientWidth - 40)/this.anchoVideo);
            // cálcular el límite de desplazamiento según número de videos
            this.limite = -(this.anchoVideo * (this.videos.length - this.nv));
        },
        /**
         * avanzar hacia la izquierda en el carrusel de vídeos
         */
        atras()
        {
            // calcular nueva posición
            this.posicion.x += this.nv * this.anchoVideo;
            this.posactual--;
            if (this.posicion.x > 0) //comporbar si se supera el límite
            {
                this.posicion.x = 0; 
                this.posactual = 1;
            } 
 
            this.vistaFlechas();
        },
        /**
         * avanzar hacia la derecha en el carrusel de vídeos
         */
        adelante()
        {
            // calcular nueva posición
            this.posicion.x -= this.nv * this.anchoVideo;
            this.posactual++;
            if (this.posactual > this.posmax)
            {
                this.posmax = this.posactual;
                // traspasar otro grupo de vídeos a mostrar
                let aux = this.videos.splice(0, this.nv);
                this.videosmostrados = this.videosmostrados.concat(aux);
            }
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
            this.$emit('verVideo', video); // enviar a componente padre datos de video a reproducir
        },
        /**
         * Ordena por fecha de publicación los vídeos, primero los más recientes
         */
        ordenFecha()
        {
            // recuperar vídeos mostrados para listado
            this.videos = this.videosmostrados.splice(0).concat(this.videos);

            // ordenar por fecha de publicación
            this.videos.sort(function comparar(elemento1, elemento2)
            {  
                if (elemento1.fecha > elemento2.fecha) return -1;
                else if (elemento1.fecha < elemento2.fecha) return 1;
                else return 0;
            });

            // desplazar hasta el inicio
            this.posicion.x = 0;
            // modificar visibilidad de fechas
            this.vistaFlechas();
            // mostrar primeros vídeos del listado
            this.cargaPrimeros();
        },
        /**
         * Ordena los vídeos por mayor número de votos positivos
         */
        ordenVotos()
        {
            // recuperar vídeos mostrados para listado
            this.videos = this.videosmostrados.splice(0).concat(this.videos);

            // ordenas por votos positivos
            this.videos.sort(function comparar(elemento1, elemento2)
            {  
                if (elemento1.estgusta > elemento2.estgusta) return -1;
                else if (elemento1.estgusta < elemento2.estgusta) return 1;
                else return 0;
            });

            // desplazar hasta el inicio
            this.posicion.x = 0;
            // modificar visibilidad de fechas
            this.vistaFlechas();
            // mostrar primeros vídeos del listado
            this.cargaPrimeros();
        },
        /**
         * Ordena los vídeo por número de reproducciones, primero el más reproducido
         */
        ordenRepro()
        {
            // recuperar vídeos mostrados para listado
            this.videos = this.videosmostrados.splice(0).concat(this.videos);

            // ordenar por número de reproducciones
            this.videos.sort(function comparar(elemento1, elemento2)
            {  
                if (elemento1.estrep > elemento2.estrep) return -1;
                else if (elemento1.estrep < elemento2.estrep) return 1;
                else return 0;
            });

            // desplazar hasta el inicio
            this.posicion.x = 0;
            // modificar visibilidad de fechas
            this.vistaFlechas();
            // mostrar primeros vídeos del listado
            this.cargaPrimeros();
        },
        ordenFiabilidad()
        {
            // recuperar vídeos mostrados para listado
            this.videos = this.videosmostrados.splice(0).concat(this.videos);

            // ordenar por índice de fiabilidad
            this.videos.sort(function comparar(elemento1, elemento2)
            {  
                if (elemento1.estrellas > elemento2.estrellas) return -1;
                else if (elemento1.estrellas < elemento2.estrellas) return 1;
                else return 0;
            });

            // desplazar hasta el inicio
            this.posicion.x = 0;
            // modificar visibilidad de fechas
            this.vistaFlechas();
            // mostrar primeros vídeos del listado
            this.cargaPrimeros();
        }
    }
}
</script>