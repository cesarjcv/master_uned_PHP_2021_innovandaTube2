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
                    </div>
                </div>
                <div class="modal-footer" style="flex-wrap:nowrap;">
<!--                    <div class="btn-group dropup">
  <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
    Acción
  </button>

  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="#">Favorito</a></li>
    <li><a class="dropdown-item" href="#">Me gusta</a></li>
    <li><a class="dropdown-item" href="#">No me gusta</a></li>
  </ul>
</div>
                    <i class="bi bi-hand-thumbs-down"></i>
                    <i class="bi bi-hand-thumbs-up"></i>
                        
                    <i class="bi bi-star"></i>
                    <i class="bi bi-star-fill"></i>-->
                     <div style="height:46px; overflow:clip; text-overflow:ellipsis; margin-right:30px; position:relative;">
                         <div style="position: absolute; right:0; bottom:0; color: #0000ff; cursor:pointer;" @click="mostrarInfo()">Ver más...</div>
                         <h5>{{titulo}}</h5>
                         <span v-html="descripcion"></span>
                         </div>  
                    
                    <!--<i class="bi bi-info-circle iconoInfo" @click="mostrarInfo()"></i>-->
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
            this.mHorizontal = 2 * this.dim.margenLateral + 2 * this.dim.margenVideo;
            this.mVertical = 2 * this.dim.margenVertical + 2 * this.dim.margenVideo + 2 * this.dim.margenBotones + this.dim.altoBotones + this.dim.margenInferior;

            this.cambioTamano();
        },

        methods:{
            setVideo(videoact)
            {
                this.video = videoact;
                this.url = "https://www.youtube.com/embed/" + this.video.videoid;
                this.titulo = this.video.titulo;
                this.descripcion = this.video.descripcion.replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll("\n", "<br />");
                this.cambioTamano();
                //console.log(this.video);
            },
            cambioTamano()
            {
                if (this.video != null)
                {
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
                    this.ancho = "max-width:" +  (this.dimVideo.ancho + 2 * this.dim.margenLateral) + "px";

                    // cuadro de información
                    let c = document.getElementById(this.identificador).querySelector(".cuadroInfo");
                    c.style.width = this.dimVideo.ancho + "px";
                    c.style.height = this.dimVideo.alto + "px";
                }
                //console.log(this.dimVideo.ancho + "X" + this.dimVideo.alto + " - " + this.video.proporcion);
                //this.nv = Math.floor((document.documentElement.clientWidth - 40)/this.anchoVideo);
                // cálcular el límite de desplazamiento según número de videos
                //this.limite = -(this.anchoVideo * (this.videos.length - this.nv));
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
            mostrarInfo()
            {
                let c = document.getElementById(this.identificador).querySelector(".cuadroInfo");
                if (c.style.display == "block") c.style.display = "none";
                else c.style.display = "block";
            }
        },
    };
</script>