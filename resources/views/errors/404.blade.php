@extends('errors.layout')

@section('title', 'Página Não Encontrada')
@section('code', '404')

@section('icon')
<div class="error-icon-box">
    <i class="fa-solid fa-compass-drafting"></i>
</div>
@endsection

@section('message')
O endereço solicitado não foi encontrado, foi alterado ou não está mais disponível. Verifique o link digitado ou volte à página inicial para continuar navegando com segurança.
@endsection
