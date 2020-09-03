require('./bootstrap');
import Chart from 'chart.js'
import { ajax } from 'jquery';
import moment from 'moment';
import 'moment/locale/es'  // without this line it didn't work
moment.locale('es')


window.Vue = require('vue');


Vue.component('example-component', require('./components/ExampleComponent.vue').default);


const app = new Vue({
    el: '#app',
});


var ctx = document.getElementById('myChart');
if (ctx) {
    window.onload = function () {
        window.myChart = new Chart(ctx, config);
    }

    var config = {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: '# of Votes',
                data: [],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    }

    $('.btn-indicador').click(function () {
        let indicador = $(this).data('indicador');

        $.getJSON(`https://mindicador.cl/api/${indicador}`, function (data) {
            var dailyIndicators = data;

            let valores = []
            let fechas = []
            dailyIndicators.serie.forEach(function (i) {
                valores.push(i.valor)
                fechas.push(moment.utc(i.fecha).format('DD/MM/YYYY'))
            });

            config.data.labels = fechas
            config.data.datasets.forEach(function (dataset) {
                dataset.label = `Valores ${dailyIndicators.nombre}`;
                dataset.data = valores

            });

            window.myChart.update();
        }).fail(function () {
            console.log('Error al consumir la API!');
        });
    });
}



