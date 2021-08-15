<template>
    <div class="modal" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="Buscar" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Búsqueda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" placeholder="Término a buscar" aria-label="Término a buscar" id="textobus" v-model="textobuscar">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="btit" v-model="titulo">
                                <label class="form-check-label" for="btit">Título</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="bdes" v-model="descripcion">
                                <label class="form-check-label" for="bdes">Descripción</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label" for="bfi">Desde</label>
                            <input class="form-control" type="date" id="bfi" v-model="finicial">
                                
                            
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="bff">Hasta</label>
                            <input class="form-control" type="date" id="bff" v-model="ffinal">
                                
                            
                        </div>
                    </div>
                </div>    
                <div class="modal-footer" style="flex-wrap:nowrap; justify-content: flex-start;">
                    <button type="button" class="btn btn-primary">Buscar</button>
                    <button type="button" class="btn btn-secondary">Cerrar</button>
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
                textobuscar: "",
                titulo: true,
                descripcion: true,
                finicial: "",
                ffinal: "",
            }
        },
        /*created() {
            window.addEventListener("resize", this.cambioTamano); // evento de cambio tamaño de página
        },
        destroyed() {
            window.removeEventListener("resize", this.cambioTamano);
        },*/
        mounted()
        {
            // cálculo de tamaños de elementos de ventana que restan zona de reproducción
            /*this.mHorizontal = 2 * this.dim.margenLateral + 2 * this.dim.margenVideo;
            this.mVertical = 2 * this.dim.margenVertical + 2 * this.dim.margenVideo + 2 * this.dim.margenBotones + this.dim.altoBotones + this.dim.margenInferior;

            this.cambioTamano();

            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function (toastEl) {
                return new bootstrap.Toast(toastEl,{autohide: false});
            });*/

        },

        methods:{
            /**
             * Establecer el vídeo a reproducir
             */
            /*setVideo(videoact)
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

            },*/
            /*formatoFecha(f)
            {
                let meses = ["", "ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];
                return parseInt(f.substring(8, 10)) + " " + meses[parseInt(f.substring(5, 7))] + " " + parseInt(f.substring(0, 4));
            },*/
            /**
             * Calcular las dimensiones de los elementos de la ventana de reproducción según el tamaño de la zona vislble del navegador
             */
            /*cambioTamano()
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

                    console.log(document.documentElement.clientWidth + " - " + document.documentElement.clientHeight + " " + 
                    document.documentElement.clientWidth/document.documentElement.clientHeight);
                    console.log(this.video.proporcion + ": " + this.dimVideo.ancho + "x" + this.dimVideo.alto);
                    console.log(this.mHorizontal + "&" + this.mVertical);
                }
            },
            /**
             * cerrar la ventana
             * @return void
             */
            /*cerrarVentana()
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
            /*mostrarInfo()
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
            /*ocultarInfo()
            {
                document.getElementById(this.identificador).querySelector(".cuadroInfo").style.display = "none";
                document.getElementById(this.identificador).querySelector(".ver_mas > .bi").className = "bi bi-chevron-double-up";
            },
            mostrarCompartir()
            {
                var myToastEl = document.getElementById('nota');
                var myToast = bootstrap.Toast.getInstance(myToastEl) // Returns a Bootstrap toast instance
                myToast.show();
            },
            ocultarCompartir()
            {
                var myToastEl = document.getElementById('nota');
                var myToast = bootstrap.Toast.getInstance(myToastEl) // Returns a Bootstrap toast instance
                myToast.hide();
            },
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
             copiarPortapapeles()
             {
                navigator.clipboard.writeText("https://youtu.be/" + this.videoid);
             }*/

        },
    };
</script>