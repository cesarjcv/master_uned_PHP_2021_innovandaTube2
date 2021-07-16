require('./bootstrap');

//const vue = require('vue');
//window.Vue = vue;
import Vue from 'vue'

// Página principal
Vue.component('principal-buscar-componente', require('./componentesVue/principal/PrincipalBuscarComponent.vue').default);
Vue.component('listas-horizontales-componente', require('./componentesVue/principal/ListasHorizontalesComponente.vue').default);
Vue.component('carrusel-componente', require('./componentesVue/principal/CarruselComponente.vue').default);
Vue.component('video-carrusel-componente', require('./componentesVue/principal/VideoCarruselComponente.vue').default);
Vue.component('flecha-componente', require('./componentesVue/principal/FlechaComponente.vue').default);
Vue.component('ver-video-componente', require('./componentesVue/principal/VerVideoComponente.vue').default);

// Administración
/* canales */
Vue.component('admin-canales-componente', require('./componentesVue/Admin/AdminCanalesComponente.vue').default);
Vue.component('dialogo-nuevo-canal-componente', require('./componentesVue/Admin/DialogoNuevoCanalComponente.vue').default);
Vue.component('entrada-canal-componente', require('./componentesVue/Admin/EntradaCanalComponente.vue').default);
/* categorías */
Vue.component('admin-categorias-componente', require('./componentesVue/Admin/AdminCategoriasComponente.vue').default);
Vue.component('dialogo-nueva-categoria-componente', require('./componentesVue/Admin/DialogoNuevaCategoriaComponente.vue').default);
Vue.component('entrada-categoria-componente', require('./componentesVue/Admin/EntradaCategoriaComponente.vue').default);
Vue.component('dialogo-mod-categoria-componente', require('./componentesVue/Admin/DialogoModCategoriaComponente.vue').default);
/* videos */
Vue.component('admin-videos-componente', require('./componentesVue/Admin/AdminVideosComponente.vue').default);
Vue.component('entrada-video-componente', require('./componentesVue/Admin/EntradaVideoComponente.vue').default);
Vue.component('dialogo-video-categorias-componente', require('./componentesVue/Admin/DialogoVideoCategoriasComponente.vue').default);
Vue.component('dialogo-previsualizar-video-componente', require('./componentesVue/Admin/DialogoPrevisualizarVideoComponente.vue').default);
/* diálogos */
Vue.component('dialogo-confirmacion-componente', require('./componentesVue/Admin/DialogoConfirmacionComponente.vue').default);

const app = new Vue({
    el: '#app',
});
window.app = app;