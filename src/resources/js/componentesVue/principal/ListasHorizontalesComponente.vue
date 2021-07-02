<template>
    <div >
        <carrusel-componente v-for="listaCat in listados" :key="listaCat.id" :categoria="listaCat">
        </carrusel-componente>
    </div>
</template>


<script>

export default {

    data() {
        return {
            listas: [1, 2],
            listados: [],
        }
    },
    mounted() {
        //console.log(JSON.stringify(this.listas));
        //console.log('principal mounted.')

        const parametros = {listas: this.listas};
        // obtener listado de categorías
        axios.put('api/categoria/convideo', parametros)
            .then((respuesta) => {
                this.listados = respuesta.data;
                //console.log(this.listados);
                /*console.log(respuesta.data);*/
            });
    },
    methods: {
        /*addHorizontal(horizontal) {
            this.videos.push(horizontal);
        },*/
    }
}
</script>