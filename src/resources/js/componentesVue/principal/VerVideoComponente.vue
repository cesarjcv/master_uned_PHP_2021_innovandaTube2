<template>
    <div class="modal fade" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="VerVideo" aria-hidden="true">
        <div class="modal-dialog" role="document" :style="ancho">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="embed-responsive">
                        <iframe :width="dimVideo.ancho" :height="dimVideo.alto" :src="url" frameborder="0" class="embed-responsive-item">
                        </iframe>
                        <div class="cuadroInfo" :width="dimVideo.ancho" :height="dimVideo.alto">
                            <button type="button" class="btn-close" aria-label="Close" @click="mostrarInfo()" style="margin-left:10px;"></button>
                            <div class="info_estadistica">
                                <i class="bi bi-play-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Número de reproducciones"></i>{{reproducciones}}
                                <i class="bi bi-hand-thumbs-up" data-bs-toggle="tooltip" data-bs-placement="top" title="Votos positivos"></i>{{megusta}}
                                <i class="bi bi-hand-thumbs-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Votos negativos"></i>{{nomegusta}}
                            </div>
                            
                            <h2>{{titulo}}</h2>
                            <p v-html="descripcion"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="flex-wrap:nowrap;">
                    <div class="ver_video_texto_inf">
                        <div class="ver_mas" @click="mostrarInfo()">Ver más...</div>
                        <h5>{{titulo}}</h5>
                        <span v-html="descripcion"></span>
                    </div>  
                    <button type="button" class="btn btn-secondary" @click="cerrarVentana()">Cerrar</button>
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
                dim: {
                    margenVertical: 28,
                    margenVideo: 16,
                    margenBotones: 12,
                    altoBotones: 46,
                    margenInferior: 10,
                    margenLateral: 20,
                },
                mVertical: 0,
                mHorizontal: 0,
                ancho: "max-width:500px",
                dimVideo: {
                    ancho: 480,
                    alto: 360,
                },
                titulo: "",
                descripcion: "",
                reproducciones: 0,
                megusta: 0,
                nomegusta: 0,
            }
        },
        created() {
            window.addEventListener("resize", this.cambioTamano); // evento de cambio tamaño de página
        },
        destroyed() {
            window.removeEventListener("resize", this.cambioTamano);
        },
        mounted()
        {
            // cálculo de tamaños de elementos de ventana que restan zona de reproducción
            this.mHorizontal = 2 * this.dim.margenLateral + 2 * this.dim.margenVideo;
            this.mVertical = 2 * this.dim.margenVertical + 2 * this.dim.margenVideo + 2 * this.dim.margenBotones + this.dim.altoBotones + this.dim.margenInferior;

            this.cambioTamano();
        },

        methods:{
            /**
             * Establecer el vídeo a reproducir
             */
            setVideo(videoact)
            {
                this.video = videoact;
                this.url = "https://www.youtube.com/embed/" + this.video.videoid; // url de youtube
                this.titulo = this.video.titulo;
                this.descripcion = this.video.descripcion.replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll("\n", "<br />");
                this.reproducciones = this.video.estrep;
                this.megusta = this.video.estgusta;
                this.nomegusta = this.video.estnogusta;
                this.cambioTamano();

            },
            /**
             * Calcular las dimensiones de los elementos de la ventana de reproducción según el tamaño de la zona vislble del navegador
             */
            cambioTamano()
            {
                if (this.video != null)
                {
                    // calcular dimensiones del frame de vídeo
                    if (document.documentElement.clientWidth/document.documentElement.clientHeight > parseFloat(this.video.proporcion))
                    {
                        this.dimVideo.alto = document.documentElement.clientHeight - this.mVertical;
                        this.dimVideo.ancho = parseInt(this.dimVideo.alto * parseFloat(this.video.proporcion));
                    }
                    else
                    {
                        this.dimVideo.ancho = document.documentElement.clientWidth - this.mHorizontal;
                        this.dimVideo.alto = parseInt(this.dimVideo.ancho / parseFloat(this.video.proporcion));
                    }
                    // ancho ventana flotante
                    this.ancho = "max-width:" +  (this.dimVideo.ancho + 2 * this.dim.margenLateral) + "px";

                    // cuadro de información
                    let c = document.getElementById(this.identificador).querySelector(".cuadroInfo");
                    c.style.width = this.dimVideo.ancho + "px";
                    c.style.height = this.dimVideo.alto + "px";
                }
            },
            /**
             * cerrar la ventana
             * @return void
             */
            cerrarVentana()
            {
                // eliminar url de iframe, para que deje de reproducirse el vídeo
                this.url = "";
                // cerrar ventana de reproducción
                let d = document.getElementById(this.identificador);
                let m = bootstrap.Modal.getInstance(d);    
                m.hide();
            },
            /**
             * Alternar entre mostrar/ocultar capa de información de vídeo
             */
            mostrarInfo()
            {
                let c = document.getElementById(this.identificador).querySelector(".cuadroInfo");
                if (c.style.display == "block") c.style.display = "none";
                else c.style.display = "block";
            },
            /**
             * Ocultar capa que muestra información de vídeo
             */
            ocultarInfo()
            {
                document.getElementById(this.identificador).querySelector(".cuadroInfo").style.display = "none";
            }
        },
    };
</script>