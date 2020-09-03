@extends('layouts.app')

@section('content')
<div class="buttons-group">
    <button class="btn btn-secondary btn-indicador" data-indicador="uf">UF</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="ivp">IVP</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="dolar">DOLAR</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="dolar_intercambio">DOLAR INTERCAMBIO</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="euro">EURO</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="ipc">IPC</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="utm">UTM</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="imacec">IMACEC</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="tpm">TPM</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="libra_cobre">LIBRA COBRE</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="tasa_desempleo">TASA DESEMPLEO</button>
    <button class="btn btn-secondary btn-indicador" data-indicador="bitcoin">BITCOIN</button>
</div>
<div class="chart-container" style="position: relative; height:350px; width:80vw">
    <canvas id="myChart" height="100px"></canvas>
</div>

@endsection