<template>
    <div class="col" style='margin:15px 0;'>
        <div class="card" style="width: 18rem;">
            <img :src="canal.imagen" class="card-img-top" :alt="canal.nombre">
            <div class="card-body overflow-auto" style='height:250px'>
                <h5 class="card-title">{{ canal.nombre }}</h5>
                <p class="card-text" v-html="this.des"></p>
            </div>
            <div class="card-footer">
                <small class="text-muted">Creado: {{ preFecha(canal.fecha) }}</small>
                <button type="button" class="btn btn-outline-danger float-end ms-2" @click="eliminar()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                </button>
                <button type="button" class="btn btn-outline-primary float-end" @click="irEnlace(canal.channelid)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                    <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/>
                    <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        /**
         * Datos del canal a mostrar
         */
        props: ['canal'],
        data(){
            return {
                des: "", // descripción con ajuste de salots de línea
            }
        },
        mounted()
        {
            // ajustar saltos de línea
            this.des = this.canal.descripcion.replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll("\n", "<br />");
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
             * enviar a componente padre un mensaje de eliminación de este canal
             */
            eliminar()
            {
                this.$emit('eliminar', this.canal.id);
            },
            /**
             * Abre nueva ventana con la dirección url del canal
             */
            irEnlace(id)
            {
                window.open("https://www.youtube.com/channel/" + id, "_blank");
            }
        }
    }
</script>