@extends('errors.layout')

@section('title', 'Acesso Restrito')
@section('code', '403')

@section('icon')
<div class="error-icon-box">
    <i class="fa-solid fa-shield-halved"></i>
</div>
@endsection

@section('message')
Você não possui permissão para acessar esta área restrita ou executar esta ação no sistema. Caso acredite que isto seja um engano, entre em contato com nosso time de suporte.
@endsection
