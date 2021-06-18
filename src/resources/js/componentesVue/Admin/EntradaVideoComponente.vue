<template>
    <div class="col" style='margin:15px 0;'>
        <div class="card" style="width: 18rem;">
            <img :src="video.imagen" class="card-img-top" :alt="video.titulo">
            <div class="card-body overflow-auto" style='height:150px'>
                <h6 class="card-title"><strong>{{ video.titulo }}</strong></h6>
                <p class="card-text" v-html="this.des"></p>
            </div>
            <div class="card-footer">
                <small class="text-muted">Creado: {{ preFecha(video.fecha) }}</small>
                <button type="button" class="btn btn-outline-primary float-end ms-2" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmarks" viewBox="0 0 16 16">
                    <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L7 13.101l-4.223 2.815A.5.5 0 0 1 2 15.5V4zm2-1a1 1 0 0 0-1 1v10.566l3.723-2.482a.5.5 0 0 1 .554 0L11 14.566V4a1 1 0 0 0-1-1H4z"/>
                    <path d="M4.268 1H12a1 1 0 0 1 1 1v11.768l.223.148A.5.5 0 0 0 14 13.5V2a2 2 0 0 0-2-2H6a2 2 0 0 0-1.732 1z"/>
                    </svg>
                </button>
                <button type="button" class="btn btn-outline-primary float-end" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445z"/>
                    </svg>
                </button>
                <div v-for="cat in video.categorias" :key="cat.idcategoria" style='margin-top:10px'>
                    <button class="categoriaVideo">
                    {{ nombreCategoria(cat.idcategoria) }}
                    </button>
                </div> 
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        /**
         * Datos del vídeo a mostrar
         */
        props: ['video', 'categorias'],
        data(){
            return {
                des: "", // descripción con ajuste de saltos de línea
                catVideos: [], // categorías del video
            }
        },
        mounted()
        {
            // ajustar saltos de línea
            if (this.video.descripcion) this.des = this.video.descripcion.replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll("\n", "<br />");
            //console.log(this.categorias);
            // cargar listado de categorías del video
            /*axios.get('/api/video/categorias/' + this.video.id).then((response) => 
            {
                this.catVideos = response.data;
                //console.log(response);
                //console.log(this.catVideos);
            });*/
        },

        methods:{
            /**
             * Cambiar fromato de fecha para mostrar
             * @param string f fecha en formato AAAA-MM-DD HH:mm:ss
             * @return string fecha en formato DD/MM/AAAA
             */
            preFecha(f)
            {
                let t = f.substring(0,10).split('-');
                return t[2] + "/" + t[1] + "/" + t[0];
            },
            /**
             * Nombre de categoiría por ID
             * @param int idcategoria identificador de categoría
             * @return string nombre de categoría
             */
            nombreCategoria(idcategoria)
            {
                for(var i=0; i < this.categorias.length; i++)
                {
                    if (this.categorias[i].id == idcategoria) return this.categorias[i].nombre;

                    return null;
                }
            },
            /**
             * enviar a componente padre un mensaje de eliminación de este canal
             */
            /*eliminar()
            {
                this.$emit('eliminar', this.canal.id);
            },*/
            /**
             * Abre nueva ventana con la dirección url del canal
             */
            /*irEnlace(id)
            {
                window.open("https://www.youtube.com/channel/" + id, "_blank");
            }*/
        }
    }
</script>