<template>
    <form class="d-flex" action=''>
        
        <input class="form-control form-control-sm me-2" type="search" placeholder="Buscar" aria-label="Buscar" v-show='visible'>
        <button class="btn " @click.prevent="pulsar()"><img src='img/lupa.png' alt='buscar'/></button>
    </form>
</template>

<script>
//btn-outline-success
    export default {
        data(){
            return {
                visible:false,
            }
        },
        mounted() {
            console.log('buscar mounted.')
        },
        methods: {
            pulsar(){
                //this.current_page = current;
                //this.getData();
                this.visible = ! this.visible;
            },
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