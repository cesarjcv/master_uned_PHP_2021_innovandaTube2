<template>
    <div class="modal fade" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="NuevoCanal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">A&ntilde;adir nuevo canal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                <label for="datocanal">URL o c&oacute;digo de canal</label>
                <input type="text" class="form-control" id="datocanal" name="datocanal" placeholder="URL o código" value="" v-model="codigo" v-bind:disabled="trabajando">
                </div>
                <div v-if="avisoVisible" class="alert alert-warning d-flex align-items-center cuadroError" role="alert">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2em;"></i>
                    <div>
                        {{ textoAviso }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" @click="nuevoCanal()" v-bind:disabled="trabajando">
                    <span v-if="trabajando" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>{{ textoBotonAceptar }}
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" v-bind:disabled="trabajando">Cancelar</button>
            </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        /**
         * Identificador para la ventana modal
         */
        props:['identificador'],

        data(){
            return {
                codigo: "", // texto del cuadro del formulario
                avisoVisible: false, // ¿se ve aviso de error?
                textoAviso: "", // texto para aviso de error
                trabajando: false, // Esperando respuesta del servidor en una operación
                textoBotonAceptar: "Aceptar", // texto del botón Aceptar
            }
        },
        mounted() {

            this.$nextTick(function () {
                // Código que se ejecutará solo después de
                // haber renderizado la vista completa
                // al mostrar ventana limpiar datos
                var v = document.getElementById(this.$props.identificador);
                v.addEventListener('shown.bs.modal', function (event) {
                    this.__vue__.limpiar();
                });
            });
        },
        methods:{
            /**
             * Enviar datos a API para crear nueva entrada de canal en base de datos
             */
            nuevoCanal() 
            {
                // parámetros a enviar
                const parametros = {dato: this.codigo};

                // modificar interfaz a estado "ocupado"
                this.trabajando = true;
                this.textoBotonAceptar = "Cargando...";
                this.avisoVisible = false;

                // llamada a API de aplicación para insertar Canal
                axios.post('/api/canal', parametros).then((respuesta) => 
                {
                    //console.log(respuesta);
                    if (respuesta.data.error) // error en operación
                    {
                        // mostrar mensaje de error
                        if (typeof respuesta.data.error === "object") this.textoAviso = respuesta.data.error.original.error;
                        else this.textoAviso = respuesta.data.error;
                        this.avisoVisible = true;
                    }
                    else
                    {
                        const canal = respuesta.data; // datos de canal
                        canal.categorias = []; // listado vacío de categorías para canal
                        this.codigo = ""; // limpiar campo
                        this.$emit('nuevoCanal', canal); // enviar a componente padre datos de nuevo canal
                    }
                    // restaurar la interfaz
                    this.trabajando = false;
                    this.textoBotonAceptar = "Aceptar";
                });

            },
            /**
             * Poner valores inciales para formulario y mensaje de error
             */
            limpiar()
            {
                this.codigo = "";
                this.avisoVisible = false;
            }
        },
    };
</script>