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
                    <div class="row" style="margin: 20px 0;">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="busqueda()">Buscar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
        props:['identificador', 'referenciabus'],
        data(){
            return {
                textobuscar: "",
                titulo: true,
                descripcion: true,
                finicial: "",
                ffinal: "",
            }
        },
        methods:{
            /**
             * 
             */
            busqueda()
            {
                //console.log(this.textobuscar);
                // comprobar valores de campos
                if (this.textobuscar.trim().length == 0)
                {
                    alert("Debe especificar un texto de búsqueda.");
                    return;
                }
                if (!this.titulo && !this.descripcion)
                {
                    alert("Debe especificar al menos uno de los campos de búsqueda, \"Título\" o \"Descripción\".");
                    return;
                }
                //vm.$refs.carruseles.buscar(this.textobuscar, this.titulo, this.descripcion, this.finicial, this.ffinal);
                let argumentos = {texto: this.textobuscar.trim(), titulo: this.titulo, des: this.descripcion, fini: this.finicial, ffin: this.ffinal};
                this.$root.$emit('busqueda', argumentos); //like this
                
                // cerrar ventana 
                let d = document.getElementById(this.identificador);
                let m = bootstrap.Modal.getInstance(d);    
                m.hide();

            },

        },
    };
</script>