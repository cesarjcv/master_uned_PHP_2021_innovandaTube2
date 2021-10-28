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
                    <i class="bi bi-trash"></i>
                </button>
                <button type="button" class="btn btn-outline-primary float-end ms-2" @click="irEnlace(canal.channelid)">
                    <i class="bi bi-link"></i>
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
                //console.log(this.canal);
                this.$emit('selCategoria', this.canal.id, this.canal.categorias);
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