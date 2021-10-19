<template>
    <div>
        <div class='anadir'>
            <button class="btn btn-primary" @click="abrirNuevoAdministrador()">Añadir administrador</button>
        </div>

        <dialogo-nuevo-administrador-componente identificador="dialogoNuevo" v-on:nuevoAdmin="nuevoAdmin" ></dialogo-nuevo-administrador-componente>

        <div class="container-fluid">
            <ul class="list-group admin">
                <li class="list-group-item d-flex justify-content-between align-items-center" v-for="adm in administradores" :key="adm.usuario">
                    {{ adm.usuario }}
                    <span class="badge bg-danger rounded-pill mano" @click="eliminar(adm.id, $event)"><i class="bi bi-x-lg"></i></span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                administradores: [], // listado de administradores
            }
        },
        mounted() {
            // obtener listado de administradores
            axios.get('/api/administrador').then((response) => 
            {
                    this.administradores = response.data;
            });
        },
        methods: {
            /**
             * Abrir ventana para nuevo administrador
             */
            abrirNuevoAdministrador() 
            {
                // abrir ventana modal
                let d = document.getElementById('dialogoNuevo');
                let x = new bootstrap.Modal(d, {backdrop: true});
                x.show();
            },
            /**
             * Añadir al listado de canales los datos de los canales añadidos
             */
            nuevoAdmin(usuario) 
            {
                // añadir al vector de administradores
                this.administradores.unshift(usuario);
                // ordenar listado alfabeticamente
                this.administradores.sort((firstEl, secondEl) => {
                    if (firstEl.usuario > secondEl.usuario) return +1;
                    else return -1;
                } );

                // cerrar ventana modal de añadir canal
                let d = document.getElementById('dialogoNuevo');
                let m = bootstrap.Modal.getInstance(d);    
                m.hide();
            },
            /**
             * Eliminar permisos de administradore para usuario 
             * Mostrar ventana modal de confirmación
             * @param int id identificador en base de datos del usuario a eliminar
             */
            eliminar(id, evento) {
                axios.delete('/api/administrador/' + id).then((response) => 
                {
                    //eliminar del listado al usuario con ese identificador
                    for(let i = 0; i < this.administradores.length; i++)
                    {
                        if (this.administradores[i].id == id)
                        {
                            this.administradores.splice(i, 1);
                            break;
                        }
                    }
                });
            },

        }
    }
</script>