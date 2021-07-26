<template>
    <div class="modal fade" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="VideoCategorias" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-body">
                <div class="embed-responsive">
                <iframe width="480" height="360" :src="url" frameborder="0" class="embed-responsive-item">
                </iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        /**
         * - Identificador para la ventana modal
         */
        props:['identificador'],
        data(){
            return {
                url: "", 
                video: null,
            }
        },

        methods:{
            /**
             * Establecer el vídeo a reproducir
             */
            setVideo(videoact)
            {
                this.video = videoact;
                this.url = "https://www.youtube.com/embed/" + this.video.videoid; // url de youtube
            },

        },
    };
</script>