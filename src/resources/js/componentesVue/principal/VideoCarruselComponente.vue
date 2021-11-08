<template>
    <li class="video" @click="verVideo()">
        <div class="text-white m-1 position-relative">
            
            <div class="gradientContainer w-100 h-100 position-absolute" >
                <div class="fiabilidad" v-if="video.estrellas > 0">
                    <i v-for="n in 5" :key="n" class="bi" :class="[video.estrellas >= n ? 'bi-star-fill text-warning' : 'bi-star']"></i>
                </div> 
                <span class="d-block position-absolute fixed-bottom titulo">{{ video.titulo }}</span>
            </div>
            <img class="border-0 w-100" v-bind:src="video.imagen" alt="">
        </div>
    </li>
</template>

<script>

export default {
    /**
     * datos de vídeo
     */
    props: ['video'],

    data() {
        return {}
    },
    mounted() {
    },
    methods: {
        /**
         * Se pulsa con ratón. Ver vídeo.
         */
        verVideo()
        {
            this.$emit('verVideo', this.video); // enviar a componente padre datos de video a reproducir
        }
    }
}
</script>