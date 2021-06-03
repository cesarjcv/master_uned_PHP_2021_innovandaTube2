<template>
    <div class="col" style='margin:15px 0;'>
        <div class="card" style="width: 18rem;">
            <img :src="canal.imagen" class="card-img-top" :alt="canal.nombre">
            <div class="card-body overflow-auto" style='height:250px'>
                <h5 class="card-title">{{ canal.nombre }}</h5>
                <p class="card-text">{{ canal.descripcion }}</p>
            </div>
            <div class="card-footer">
                <small class="text-muted">Creado: {{ preFecha(canal.fecha) }}</small>
                <button type="button" class="btn btn-outline-danger float-end" @click="eliminar()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
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
            }
        }
    }
</script>