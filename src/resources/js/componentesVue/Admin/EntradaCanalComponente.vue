<template>
    <div class="col" style='margin:15px 0;'>
        <div class="card" style="width: 18rem;">
            <img :src="canal.imagen" class="card-img-top" :alt="canal.nombre">
            <div class="card-body overflow-auto" style='height:250px'>
                <h5 class="card-title">{{ canal.nombre }}</h5>
                <p class="card-text" v-html="this.des"></p>
            </div>
            <div class="card-footer">
                <small class="text-muted" style="padding-bottom: 15px; display: inline-block;">{{ preFecha(canal.fecha) }}</small>
                <button type="button" class="btn btn-outline-danger float-end ms-2" @click="eliminar()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                </button>
                <button type="button" class="btn btn-outline-primary float-end ms-2" @click="irEnlace(canal.channelid)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                    <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/>
                    <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"/>
                    </svg>
                </button>
                <button type="button" class="btn btn-outline-success float-end" @click="ventanaCategoria()">
                    <i class="bi bi-bookmark-plus"></i>
                </button>
                <div v-for="cat in canal.categorias" :key="cat.idcategoria" style='margin-top:10px'>
                    <button class="categoriaCanal">
                        {{ nombreCategoria(cat.idcategoria) }}
                        <img src="/img/eliminar.png" @click="eliminarCategoria(cat.idcategoria)"/>
                    </button>
                </div> 
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        /**
         * Datos del canal a mostrar
         */
        props: ['canal', 'categorias'],
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
            },
            /**
             * Abrir ventana para seleccionar categoría a ñadir al canal
             * enviar mensaje a componente padre
             */
            ventanaCategoria()
            {
                this.$emit('selCategoria', this.canal.id/*, this.video.categorias*/);
            },
            /**
             * Eliminar la categoría para el canal
             * @param idcat identificador de categoría
             */
            eliminarCategoria(idcat)
            {
                const parametros = {data: {cat: idcat}};
                //this.selCatTrabajando = true;
                //console.log(idcat);

                // llamada a API de aplicación para asignar categoría
                axios.delete('/api/canal/categorias/' + this.canal.id, parametros).then((respuesta) => 
                {
                    if (respuesta.data.error) // error en operación
                    {
                        alert(respuesta.data.error);
                    }
                    else
                    {
                        // crear lista de objetos categorías
                        /*let lCat = new Array();
                        for (let i=0; i < listaid.length; i++)
                        {
                            lCat.push(new Object({idcategoria: listaid[i]}));
                        }*/
                        // buscar entrada de video y modificar su listado de categorías
                        /*for(let i=0; i < this.canales.length; i++)
                        {
                            if (this.canales[i].id == this.catCanalid)
                            {
                                this.canales[i].categorias.push({idcategoria: catid}); // añadir nueva categoría
                                console.log(this.canales[i].categorias);
                                break;
                            } 
                        }*/
                        // eliminar del listado
                        //console.log(this.canal.categorias.indexOf(idcat));
                        // buscar elemento en listado
                        let i;
                        for(i=0; i < this.canal.categorias.length; i++)
                        {
                            if (this.canal.categorias[i].idcategoria == idcat) break;
                        }
                        // aliminar de listado
                        this.canal.categorias.splice(i, 1);
                    }
                    //this.selCatTrabajando = false;
                });
            },
            /**
             * Nombre de categoiría por ID
             * @param int idcategoria identificador de categoría
             * @return string nombre de categoría
             */
            nombreCategoria(idcategoria)
            {
                for(var i=0; i < this.categorias.length; i++)
                {
                    if (this.categorias[i].id == idcategoria) return this.categorias[i].nombre;
                }
                return null;
            },
        }
    }
</script>