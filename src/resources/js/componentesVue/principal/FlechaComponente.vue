<template>
    <div  v-bind:class="clase" v-on:click="$emit(flecha.evento)" v-if="flecha.visible">
        <span class="flecha text-white">
            <i v-if="flecha.sentido == 'i'" class="bi bi-chevron-double-left"></i>
            <i v-if="flecha.sentido == 'd'" class="bi bi-chevron-double-right"></i>
         </span>
    </div>
</template>

<script>


export default {
    /**
     * datos de flechas
     */
    props: ['flecha'],

    data() {
        return {
            clase: "contenedorflecha "
        }
    },
    mounted() {
        // segun el sentido de la fecha seleccionamos una clase CSS
        if (this.flecha.sentido == 'd') this.clase += "derecha";
        else if (this.flecha.sentido == 'i') this.clase += "izquierda";
    },
}
</script>
