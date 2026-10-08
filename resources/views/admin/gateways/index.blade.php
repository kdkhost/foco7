@extends('admin.layouts.app')

@section('title', 'Gateways de Pagamento')
@section('page_title', 'Integração de Gateways & Webhooks')
@section('page_description', 'Configure credenciais de Checkout Transparente para Mercado Pago e Efí PRO (Gerencianet).')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <form action="{{ route('admin.gateways.salvar') }}" method="POST" class="ajax-form">
            @csrf

            <!-- MERCADO PAGO -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-hand-holding-usd me-2"></i> Mercado Pago (Checkout Transparente)</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="mp_ativo" id="mp_ativo" value="1" {{ $configs['mp_ativo'] ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="mp_ativo">Ativo</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Public Key *</label>
                        <input type="text" name="mp_public_key" class="form-control" value="{{ $configs['mp_public_key'] }}" placeholder="APP_USR-xxxx...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Access Token *</label>
                        <input type="password" name="mp_access_token" class="form-control" value="{{ $configs['mp_access_token'] }}" placeholder="APP_USR-xxxx...">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="mp_sandbox" id="mp_sandbox" value="1" {{ $configs['mp_sandbox'] ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="mp_sandbox">Ambiente de Testes (Sandbox)</label>
                    </div>
                </div>
            </div>

            <!-- EFÍ PRO (GERENCIANET) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-warning"><i class="fas fa-bolt me-2"></i> Efí PRO (Gerencianet - PIX / Boleto)</h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="efi_ativo" id="efi_ativo" value="1" {{ $configs['efi_ativo'] ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="efi_ativo">Ativo</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Client ID *</label>
                        <input type="text" name="efi_client_id" class="form-control" value="{{ $configs['efi_client_id'] }}" placeholder="Client_Id_xxxx...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Client Secret *</label>
                        <input type="password" name="efi_client_secret" class="form-control" value="{{ $configs['efi_client_secret'] }}" placeholder="Client_Secret_xxxx...">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="efi_sandbox" id="efi_sandbox" value="1" {{ $configs['efi_sandbox'] ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold" for="efi_sandbox">Ambiente de Homologação (Sandbox)</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">
                <i class="fas fa-save me-1"></i> SALVAR CONFIGURAÇÕES DE PAGAMENTO
            </button>
        </form>
    </div>

    <!-- URLs de Webhook -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-network-wired text-danger me-2"></i> Webhooks de Retorno Automático</h6>
            </div>
            <div class="card-body p-4 small text-secondary">
                <p>Cadastre estas URLs nos painéis dos gateways para que as faturas sejam baixadas automaticamente quando o cliente pagar:</p>

                <label class="fw-bold text-white mb-1">Webhook Mercado Pago:</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $webhookUrlMp }}" readonly id="urlMp">
                    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copiarTexto('urlMp')">Copiar</button>
                </div>

                <label class="fw-bold text-white mb-1">Webhook Efí PRO (PIX):</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $webhookUrlEfi }}" readonly id="urlEfi">
                    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copiarTexto('urlEfi')">Copiar</button>
                </div>

                <div class="alert alert-info py-2 small mb-0">
                    <i class="fas fa-info-circle me-1"></i> Os webhooks processam o pagamento em segundo plano sem necessidade de intervenção manual.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copiarTexto(id) {
        var el = document.getElementById(id);
        el.select();
        el.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(el.value);
        window.foco7Notify.success('URL de Webhook copiada!');
    }
</script>
@endsection
