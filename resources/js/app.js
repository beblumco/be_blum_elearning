/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

import { createApp } from "vue"; //IMPORT CREATE APP METHOD FROM VUE PACKAGE
import MixinHelper from "./components/mixin_helpers/functions_mixin.js";
import Select2MultipleControl from 'v-select2-multiple-component';

let files = [];
let filesCreateTraining = require.context('../../Modules/Trainings/Resources/js/components/CreateTraining', true, /\.vue$/i);
let filesTrainingExcecution = require.context('../../Modules/Trainings/Resources/js/components/TrainingExcecution', true, /\.vue$/i);
let filesTrainingCertificates = require.context('../../Modules/Trainings/Resources/js/components/Certificates', true, /\.vue$/i);
let filesWebinarsExcecution = require.context('../../Modules/Trainings/Resources/js/components/WebinarsExcecution', true, /\.vue$/i);

let filesAuth = require.context('./components/pages/auth', true, /\.vue$/i);
let filesWelcome = require.context('./components/pages/welcome', true, /\.vue$/i);
let filesOtros = require.context('./components/pages/otros', true, /\.vue$/i);
let filesModalsAdministration = require.context('../../Modules/Administration/Resources/js/components/modals', true, /\.vue$/i);
let filesAdministration = require.context('../../Modules/Administration/Resources/js/components', true, /\.vue$/i);
let filesReports = require.context('../../Modules/Accompaniment/Resources/js/components', true, /\.vue$/i);
let filesAccompaniment = require.context('../../Modules/Reports/Resources/js/components', true, /\.vue$/i);
let filesDashboard = require.context('../../Modules/Dashboards/Resources/js/components', true, /\.vue$/i);
let filesDrive = require.context('../../Modules/Drive/Resources/js/components', true, /\.vue$/i);
let filesShopCatalogue = require.context('../../Modules/Shop/Resources/js/components/catalogue', true, /\.vue$/i);
let filesShoppingCar = require.context('../../Modules/Shop/Resources/js/components/shoppingcar', true, /\.vue$/i);
let filesHistorical = require.context('../../Modules/Shop/Resources/js/components/historical', true, /\.vue$/i);
let filesShopReports = require.context('../../Modules/Shop/Resources/js/components/reports', true, /\.vue$/i);
let filesIa = require.context('../../Modules/IA/Resources/js/components', true, /\.vue$/i);

files = files.concat(filesAuth);
files = files.concat(filesCreateTraining);
files = files.concat(filesTrainingExcecution);
files = files.concat(filesWebinarsExcecution);
files = files.concat(filesTrainingCertificates);
files = files.concat(filesWelcome);
files = files.concat(filesOtros);
files = files.concat(filesModalsAdministration);
files = files.concat(filesAdministration);
files = files.concat(filesAccompaniment);
files = files.concat(filesDashboard);
files = files.concat(filesDrive);
files = files.concat(filesReports);
files = files.concat(filesShopCatalogue);
files = files.concat(filesShoppingCar);
files = files.concat(filesHistorical);
files = files.concat(filesShopReports);
files = files.concat(filesIa);

let objects_components = {};

files.forEach(file => {
        file.keys().map(key => {
                objects_components[key.split('/').pop().split('.')[0]] = file(key).default;
        })
});

const app = createApp({
        components: objects_components
});

app.config.globalProperties.$FormatCOMoney = function (value) {
        // Agrega un espacio después de "COP" para separar el símbolo del valor
        const simbolo = '$';

        // Formatear el valor para separar miles y decimales con puntos y comas
        const partes = parseFloat(value).toFixed(0).toString().split('.');
        const valorFormateado = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Combinar el símbolo y el valor formateado
        const formato = simbolo + valorFormateado;

        return formato;
};

app.config.globalProperties.$OnlyNumbers = function (control) {
        control.value = control.value.replace(/[^0-9]/g,'');
};

app.component('Select2MultipleControl', Select2MultipleControl );

app.mixin(MixinHelper);
app.mount('#app');
