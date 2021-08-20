<template>
    <form class="d-flex" action=''>
        
        <input class="form-control form-control-sm me-2" type="search" placeholder="Buscar" aria-label="Buscar" @keydown.enter.prevent="entradaTexto" v-model="texto">
        <button class="btn " @click.prevent="pulsar()"><img src='img/lupa.png' alt='buscar'/></button>
    </form>
</template>

<script>
//btn-outline-success
    export default {
        /**
         * - Identificador para la ventana de búsqueda
         */
        props:['vbus'],
        data(){
            return {
                //visible:false,
                texto : ""
            }
        },
        mounted() {
            //console.log('buscar mounted.')
        },
        methods: {
            pulsar()
            {
                //console.log(this.vbus)
                // abrir ventana de búsqueda
                let d = document.getElementById(this.vbus);
                let x = new bootstrap.Modal(d, {backdrop: 'static'});
                x.show();
            },
            entradaTexto(event)
            {
                if (this.texto.trim().length > 0)
                {
                    let argumentos = {texto: this.texto.trim(), titulo: true, des: true, fini: "", ffin: ""};
                    this.$root.$emit('busqueda', argumentos);
                }
                //console.log(event.key);
                //event.target.value += event.key;
            }
            /*getData(){
                axios.get('api/list?page=' + this.current_page)
                    .then((response) => {
                        this.videos = response.data.data;
                        this.current_page = response.data.current_page;
                        this.first_page_url = response.data.first_page_url;
                        this.last_page = response.data.last_page;
                        this.last_page_url = response.data.last_page_url;
                        this.next_page_url = response.data.next_page_url;
                        this.path = response.data.path;
                        this.per_page = response.data.per_page;
                        this.prev_page_url = response.data.prev_page_url;
                        this.to = response.data.to;
                        this.total = response.data.total;
                    });
            },
            insertarVideo(video) {
                //this.videos.push(video);
                this.getData(); // recargar listado
                this.nuevovisible = false; // cerrar ventana
            }*/
        }
    }
</script>