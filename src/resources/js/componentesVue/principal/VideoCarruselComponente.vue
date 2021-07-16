<template>
    <li class="video" @click="verVideo()">
        <div class="text-white m-1 position-relative"> 
            <div class="gradientContainer w-100 h-100 position-absolute" >
                <span class="d-block position-absolute fixed-bottom titulo">{{ video.titulo }}</span>
            </div>
            <img class="border-0 w-100" v-bind:src="video.imagen" alt="">
        </div>
    </li>
</template>

<script>

//let url_youtube = "https://img.youtube.com/vi/";

export default {

    props: ['video'],

    data() {
        return {}
    },
    mounted() {
        //console.log('Videos mounted.')
    },
    methods: {
        /**
         * Se pulsa con ratón. Ver vídeo.
         */
        verVideo()
        {
            console.log("videocarrusel");
            this.$emit('verVideo', this.video); // enviar a componente padre datos de video a reproducir
        }
        /*getImgUrl(hash) {
            return url_youtube + hash + '/mqdefault.jpg'
        },
        getTitle(video) {

            if (video.data != null && video.data != "") {

                let dataParse = JSON.parse(video.data);

                return this.truncateTitulo(dataParse.items[0].snippet.title);

            } else {
                return 'No data';
            }
        },
        truncateTitulo(str) {
            if (str.length > 36) {
                return str.substring(0, 29) + '...';
            } else {
                return str;
            }
        }*/
    }
}
</script>