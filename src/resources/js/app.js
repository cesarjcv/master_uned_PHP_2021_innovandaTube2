require('./bootstrap');

//const vue = require('vue');
//window.Vue = vue;
import Vue from 'vue'

// Página principal
Vue.component('principal-buscar-componente', require('./componentesVue/principal/PrincipalBuscarComponent.vue').default);

// Administración
/* canales */
Vue.component('admin-canales-componente', require('./componentesVue/Admin/AdminCanalesComponente.vue').default);
Vue.component('dialogo-nuevo-canal-componente', require('./componentesVue/Admin/DialogoNuevoCanalComponente.vue').default);
Vue.component('entrada-canal-componente', require('./componentesVue/Admin/EntradaCanalComponente.vue').default);

Vue.component('dialogo-confirmacion-componente', require('./componentesVue/Admin/DialogoConfirmacionComponente.vue').default);

const app = new Vue({
    el: '#app',
});
window.app = app;