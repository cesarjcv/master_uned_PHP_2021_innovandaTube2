<template>
    <div class="modal fade" :id="identificador" tabindex="-1" role="dialog" aria-labelledby="NuevaCategoria" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">A&ntilde;adir nueva categor&iacute;a</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                <label for="datocanal">Nombre de categor&iacute;a</label>
                <input type="text" class="form-control" id="datocat" name="datocat" placeholder="Nombre" value="" v-model="nombre" v-bind:disabled="trabajando" maxlength="100">
                <label for="datocanal" style='margin-top:15px'>Descripci&oacute;n</label>
                <textarea class="form-control" id="descat" name="descat" value="" v-model="descrip" v-bind:disabled="trabajando" maxlength="250">
                </textarea>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </symbol>
                </svg>
                <div v-if="avisoVisible" class="alert alert-warning d-flex align-items-center cuadroError" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                    <div>
                        {{ textoAviso }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" @click="nuevaCategoria()" v-bind:disabled="trabajando">
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
                nombre: "", // texto del cuadro del formulario
                descrip: "", // texto de la descripción
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
             * Enviar datos a API para crear nueva categoría en base de datos
             */
            nuevaCategoria() 
            {
                // parámetros a enviar
                const parametros = {nombre: this.nombre, des: this.descrip};

                // mostrar interfaz en modo "ocupado"
                this.trabajando = true;
                this.textoBotonAceptar = "Cargando...";
                this.avisoVisible = false;

                // llamada a API de aplicación para insertar Canal
                axios.post('/api/categoria', parametros).then((respuesta) => 
                {
                    if (respuesta.data.error) // error en operación
                    {
                        // mostrar mensaje de error
                        this.textoAviso = respuesta.data.error;
                        this.avisoVisible = true;
                    }
                    else
                    {
                        const categoria = respuesta.data;
                        this.nombre = "";
                        this.descrip = "";
                        this.$emit('nuevaCat', categoria); // enviar a componente padre datos de nueva categoría
                    }
                    // recuperar interfaz normal
                    this.trabajando = false;
                    this.textoBotonAceptar = "Aceptar";
                });

            },
            /**
             * Poner valores inciales para formulario y mensaje de error
             */
            limpiar()
            {
                this.nombre = "";
                this.descrip = "";
                this.avisoVisible = false;
            }
        },
    };
</script>