@extends('errors.layout')

@section('title', 'Instabilidade Temporária no Servidor')
@section('code', '500')

@section('icon')
<div class="error-icon-box">
    <i class="fa-solid fa-server"></i>
</div>
@endsection

@section('message')
Detectamos uma instabilidade inesperada ao processar sua requisição no servidor. Nossos engenheiros de monitoramento 24h já foram notificados para normalizar o serviço com agilidade.
@endsection
