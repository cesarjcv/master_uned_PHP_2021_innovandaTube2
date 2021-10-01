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
                            <h2>{{titulo}}</h2>
                            <p v-html="descripcion"></p>
                        </div>
                        <div class="toast-container position-absolute p-3 top-50 start-50 translate-middle" id="toastPlacement">
                            <div class="toast" id='nota'>
                                <div class="toast-header">
                                    <strong class="me-auto">Compartir</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                 <div class="toast-body" style="opacity:1; background-color:#fff">
                                
                                    <div class="container">
                                        <div class="row row-cols-5">
                                            <div class="col">
                                                <img src="img/correo.png" @click="abrirEnlace('correo')"/>
                                            </div>
                                            <div class="col">
                                                <img src="img/whatsapp.png" @click="abrirEnlace('whatsapp')"/>
                                            </div>
                                            <div class="col">
                                                <img src="img/telegram.png" @click="abrirEnlace('telegram')"/>
                                            </div>
                                            <div class="col">
                                                <img src="img/facebook.png" @click="abrirEnlace('facebook')"/>
                                            </div>
                                            <div class="col">
                                                <img src="img/twitter.png" @click="abrirEnlace('twitter')"/>
                                            </div>
                                        </div>
                                        <div class="row row-cols-5 textosiconoscompartir" >
                                            <div class="col">
                                                Correo
                                            </div>
                                            <div class="col">
                                                WhatsApp
                                            </div>
                                            <div class="col">
                                                Telegram
                                            </div>
                                            <div class="col">
                                                Facebook
                                            </div>
                                            <div class="col">
                                                Twitter
                                            </div>
                                        </div>
                                        <div class="row row-cols-5">
                                            <div class="col-12 enlace">
                                                https://youtu.be/{{videoid}}
                                                <div class="copiar" @click="copiarPortapapeles()">COPIAR</div>
                                            </div>
                                         </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="flex-wrap:nowrap; justify-content: flex-start;">
                    <div class="ver_video_texto_inf">
                        <div class="ver_mas" @click="mostrarInfo()"><i class="bi bi-chevron-double-up"></i><br/>Ver más...</div>
                        <h5>{{titulo}}</h5>
                        <div class="info_estadistica">
                            <div class="nreproducciones">
                                <i class="bi bi-play-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Número de reproducciones"></i>{{reproducciones}}
                                - {{fechapub}}
                            </div>
                            <div class="votos">
                                <i class="bi bi-hand-thumbs-up" data-bs-toggle="tooltip" data-bs-placement="top" title="Votos positivos"></i>{{megusta}}
                                <i class="bi bi-hand-thumbs-down" data-bs-toggle="tooltip" data-bs-placement="top" title="Votos negativos"></i>{{nomegusta}}
                                
                            </div>
                            <div @click="mostrarCompartir()" class="compartir"><i class="bi bi-reply"></i>COMPARTIR</div>
                        </div>
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
                url: "",  // url de vídeo
                video: null, // vídeo a reproducir
                dim: { // dimensiones para reproducción
                    margenVertical: 28,
                    margenVideo: 16,
                    margenBotones: 12,
                    altoBotones: 60,
                    margenInferior: 10,
                    margenLateral: 20,
                },
                mVertical: 0,
                mHorizontal: 0,
                ancho: "max-width:500px",
                dimVideo: { // dimensiones vídeo
                    ancho: 480,
                    alto: 360,
                },
                // datos del vídeo
                titulo: "",
                descripcion: "",
                reproducciones: 0,
                megusta: 0,
                nomegusta: 0,
                fechapub: "",
                videoid: "",
            }
        },
        // al crear componente
        created() {
            window.addEventListener("resize", this.cambioTamano); // evento de cambio tamaño de página
        },
        // al eliminar componente
        destroyed() {
            window.removeEventListener("resize", this.cambioTamano); // eliminar evento
        },
        mounted()
        {
            // cálculo de tamaños de elementos de ventana que restan zona de reproducción
            this.mHorizontal = 2 * this.dim.margenLateral + 2 * this.dim.margenVideo;
            this.mVertical = 2 * this.dim.margenVertical + 2 * this.dim.margenVideo + 2 * this.dim.margenBotones + this.dim.altoBotones + this.dim.margenInferior;

            // cálculo de dimensiones para vídeo
            this.cambioTamano();

            // configuración para notas emergentes
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function (toastEl) {
                return new bootstrap.Toast(toastEl,{autohide: false});
            });

        },

        methods:{
            /**
             * Establecer el vídeo a reproducir
             */
            setVideo(videoact)
            {
                this.video = videoact;
                this.videoid = this.video.videoid;
                this.url = "https://www.youtube.com/embed/" + this.video.videoid; // url de youtube
                this.titulo = this.video.titulo;
                this.descripcion = this.video.descripcion.replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll("\n", "<br />");
                this.reproducciones = this.video.estrep;
                this.megusta = this.video.estgusta;
                this.nomegusta = this.video.estnogusta;
                this.fechapub = this.formatoFecha(this.video.fecha);
                this.cambioTamano();

            },
            /**
             * Cambiar el formato de fecha a mes con letras
             */
            formatoFecha(f)
            {
                let meses = ["", "ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];
                return parseInt(f.substring(8, 10)) + " " + meses[parseInt(f.substring(5, 7))] + " " + parseInt(f.substring(0, 4));
            },
            /**
             * Calcular las dimensiones de los elementos de la ventana de reproducción según el tamaño de la zona vislble del navegador
             */
            cambioTamano()
            {
                if (this.video != null)
                {
                    // calcular dimensiones del frame de vídeo
                    if ((document.documentElement.clientWidth - this.mHorizontal)/(document.documentElement.clientHeight - this.mVertical)
                        > parseFloat(this.video.proporcion))
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
                let vm = document.getElementById(this.identificador).querySelector(".ver_mas > .bi");
                if (c.style.display == "block")
                {
                    c.style.display = "none";
                    vm.className = "bi bi-chevron-double-up";
                } 
                else
                {
                    c.style.display = "block";
                    vm.className = "bi bi-chevron-double-down";
                } 

                
            },
            /**
             * Ocultar capa que muestra información de vídeo
             */
            ocultarInfo()
            {
                document.getElementById(this.identificador).querySelector(".cuadroInfo").style.display = "none";
                document.getElementById(this.identificador).querySelector(".ver_mas > .bi").className = "bi bi-chevron-double-up";
            },
            /**
             * mostrar el panel para compartir vídeo
             */
            mostrarCompartir()
            {
                var myToastEl = document.getElementById('nota');
                var myToast = bootstrap.Toast.getInstance(myToastEl) // instancia de Bootstrap toast
                myToast.show();
                document.getElementById('toastPlacement').style.display = 'block';
            },
            /**
             * ocultar el panel de compartir
             */
            ocultarCompartir()
            {
                var myToastEl = document.getElementById('nota');
                var myToast = bootstrap.Toast.getInstance(myToastEl) // instancia de Bootstrap toast
                myToast.hide();
                document.getElementById('toastPlacement').style.display = 'none';
            },
            /**
             * Abrir un enlace del panel de compartir
             * Según la opción se utilizará el formato correspondiente
             */
            abrirEnlace(sel)
            {
                switch (sel) 
                {
                    case 'correo':
                        window.open("mailto:?subject=Vídeo: " + escape(this.titulo) + "&body=" + escape("https://youtu.be/" + this.videoid));
                        break;
                    case 'whatsapp':
                        window.open("whatsapp://send?text=" + escape(this.titulo + " https://youtu.be/" + this.videoid));
                        break;
                    case 'telegram':
                        window.open("tg://msg_url?text=" + encodeURIComponent(this.titulo) + "&url=https%3A%2F%2Fyoutu.be%2F" + this.videoid);
                        break;
                    case 'facebook':
                        window.open("https://www.facebook.com/dialog/share?app_id=87741124305&href=https%3A%2F%2Fyoutube.com%2Fwatch%3Fv%3D" + this.videoid + "%26feature%3Dshare&display=popup");
                        break;
                    case 'twitter':
                        window.open("https://twitter.com/intent/tweet?url=https%3A//youtu.be/" + this.videoid + "&text=" + escape(this.titulo) + "&via=YouTube&related=YouTube,YouTubeTrends,YTCreators");
                        break;
                 }
             },
             /**
              * Copiar el contenido del enlace al portapapeles del sistema
              */
             copiarPortapapeles()
             {
                navigator.clipboard.writeText("https://youtu.be/" + this.videoid);
             }

        },
    };
</script>