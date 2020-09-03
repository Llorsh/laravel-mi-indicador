@extends('layouts.app')

@section('content')
<div class="buttons-group">
    <button class="btn btn-primary" id="btn-reset_historico-uf">RESET GRAFICO</button>
    <br>
    <small>PARA EDITAR GRAFICO HACER CLIC EN EL VALOR RESPECTIVO</small>
    <br>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-add-uf">
        AÑADIR
    </button>
</div>
<div class="chart-container" style="position: relative; height:350px; width:80vw">
    <canvas id="chart-historico-uf" height="100px"></canvas>
</div>
<p>Solo puede existir un "VALOR" por FECHA</p>


<!-- Modal -->
<div class="modal fade" id="modal-add-uf" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Añadir nuevo valor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="form-add-uf" method="POST">
                    
                    @csrf
                    <div class="form-group">
                        <label for="fecha-add">Fecha</label>
                        <input type="date" class="form-control" name="fecha" id="fecha-add"
                            aria-describedby="help-fecha-add" placeholder="" required>
                        <small id="help-fecha-add" class="form-text text-muted">Fecha</small>
                    </div>
                    <div class="form-group">
                        <label for="valor">Valor</label>
                        <input type="number" step=".01" class="form-control" name="valor" id="valor"
                            aria-describedby="help-valor" placeholder="Valor" required>
                        <small id="help-valor" class="form-text text-muted">Valor UF</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Añadir</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="modal-edit-uf" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-uf">sm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="form-edit-uf" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="fecha" name="fecha" value="">
                    <div class="form-group">
                        <label for="valor">Valor</label>
                        <input type="number" step=".01" class="form-control" name="valor" id="valor-edit"
                            aria-describedby="help-valor" placeholder="Valor" required>
                        <small id="help-valor" class="form-text text-muted">Valor UF</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>

                <button class="btn btn-danger btn-sm mt-3" id="btn-delete-valor" data-fecha="">ELIMINAR</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var fecha_select;
    var ctx_uf = document.getElementById('chart-historico-uf');

        $('#btn-reset_historico-uf').click(function () {

            $.ajax({
                type: "GET",
                url: "/grafico/uf-reset",
                data: "",
                dataType: "Json",
                success: function (response) {
                    alert(response.msg)
                    charge_chart_uf()
                }
            });
        });


        function charge_chart_uf() {
            $.ajax({
                type: "GET",
                url: "/grafico/uf",
                data: "",
                dataType: "Json",
                success: function (response) {
                    let valores = []
                    let fechas = []
                    response.valores.forEach(function (i) {
                        valores.push(i.valor)
                    });
                    response.fechas.forEach(function (i) {
                        fechas.push(i.fecha)
                    });

                    config.data.labels = fechas
                    config.data.datasets.forEach(function (dataset) {
                        dataset.data = valores

                    });

                    window.myChart.update();
                }
            });
        }
        charge_chart_uf();

        window.onload = function () {
        window.myChart = new Chart(ctx_uf, config);
    }

    var config = {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Valores UF',
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
            },
            onClick: function(c, i){
                if(i.length > 0){
                    e = i[0];
                    var x_value = this.data.labels[e._index];
                    var y_value = this.data.datasets[0].data[e._index];

                    $('#modal-edit-uf').modal('show')
                    $('#title-uf').text(`Fecha: ${x_value}`)
                    $('#fecha').val(x_value)
                    $('#valor-edit').val(y_value)
                    fecha_select = x_value;


                }
            }
        }
    }
    
    $('#form-edit-uf').submit(function (e) { 
        e.preventDefault();

        let serialize = $(this).serialize()
        let fecha = $('#fecha').val()

        $.ajax({
            type: "PUT",
            url: `/grafico/uf/${fecha}`,
            data: serialize,
            dataType: "Json",
            success: function (response) {
               alert(response.msg)
               $('#modal-edit-uf').modal('hide')
               charge_chart_uf()
            }
        });
        
    });

    $('#form-add-uf').submit(function(e){
        e.preventDefault()

        let serialize = $(this).serialize()

        $.ajax({
            type: "POST",
            url: `/grafico/uf`,
            data: serialize,
            dataType: "Json",
            success: function (response) {
               alert(response.msg)
               $('#modal-add-uf').modal('hide')
               charge_chart_uf()
               $('#form-add-uf').trigger('reset')
            }
        });
    });

    $('#btn-delete-valor').click(function(e){
        e.preventDefault()

        $.ajax({
            type: "DELETE",
            url: `/grafico/uf/${fecha_select}`,
            data: {
                _token : '{{ csrf_token() }}'
            },
            dataType: "Json",
            success: function (response) {
                alert(response.msg)
                $('#modal-edit-uf').modal('hide')
                charge_chart_uf()
            }
        });
    })

</script>
@endpush