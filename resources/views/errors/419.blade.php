@extends('errors.layout')

@section('title', 'Sessão Expirada por Inatividade')
@section('code', '419')

@section('icon')
<div class="error-icon-box">
    <i class="fa-solid fa-hourglass-end"></i>
</div>
@endsection

@section('message')
Por motivos de segurança e integridade das informações, seu token de validação expirou após um período sem interação. Por favor, atualize a página ou refaça o login.
@endsection
