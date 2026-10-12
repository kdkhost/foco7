<?php

namespace App\Services;

class PermissaoService
{
    /**
     * Lista completa dos módulos e permissões organizadas por categoria
     */
    public const MODULOS = [
        'visao_geral' => [
            'titulo' => 'Visão Geral & Métricas',
            'permissoes' => [
                'dashboard' => [
                    'nome' => 'Dashboard Principal',
                    'descricao' => 'Visualizar gráficos, analítico de visitas e KPIs da empresa.',
                ],
            ],
        ],
        'atendimento_servicos' => [
            'titulo' => 'Operações & Atendimentos',
            'permissoes' => [
                'agendamentos' => [
                    'nome' => 'Agenda & Ordens de Serviço',
                    'descricao' => 'Gerenciar instalações e agendamentos no FullCalendar.',
                ],
                'estetica' => [
                    'nome' => 'Estética Automotiva',
                    'descricao' => 'Gerenciar serviços, itens inclusos e preços de detalhamento.',
                ],
                'planos' => [
                    'nome' => 'Planos & Mensalidades',
                    'descricao' => 'Criar e editar planos de rastreamento veicular e benefícios.',
                ],
            ],
        ],
        'financeiro_clientes' => [
            'titulo' => 'Financeiro & Clientes',
            'permissoes' => [
                'clientes' => [
                    'nome' => 'Base de Clientes',
                    'descricao' => 'Gerenciar cadastros, veículos e dados dos assinantes.',
                ],
                'faturas' => [
                    'nome' => 'Faturas & Carnês',
                    'descricao' => 'Emitir faturas avulsas, gerar carnês em PDF e controlar cobranças.',
                ],
                'gateways' => [
                    'nome' => 'Gateways de Pagamento',
                    'descricao' => 'Configurar credenciais Mercado Pago, Efí PRO e Régua de Cobrança.',
                ],
            ],
        ],
        'site_conteudo' => [
            'titulo' => 'Site, Filiais & Conteúdo CMS',
            'permissoes' => [
                'cms' => [
                    'nome' => 'Editor CMS do Site',
                    'descricao' => 'Gerenciar banners, carrossel de fotos e textos das seções.',
                ],
                'filiais' => [
                    'nome' => 'Unidades & Mapa do Brasil',
                    'descricao' => 'Cadastrar filiais, definir rotas e marcadores no OpenStreetMap.',
                ],
                'parceiros' => [
                    'nome' => 'Empresas Parceiras',
                    'descricao' => 'Gerenciar logos do carrossel infinito e marcas conveniadas.',
                ],
                'depoimentos' => [
                    'nome' => 'Moderação de Depoimentos',
                    'descricao' => 'Aprovar, rejeitar e moderar avaliações de clientes.',
                ],
                'seo' => [
                    'nome' => 'SEO & Google',
                    'descricao' => 'Otimização de meta tags, sitemap e score em tempo real.',
                ],
            ],
        ],
        'comunicacao_mensagens' => [
            'titulo' => 'Comunicação & Mensagens',
            'permissoes' => [
                'notificacoes' => [
                    'nome' => 'Central de Notificações',
                    'descricao' => 'Ler alertas de leads, disparar Web Push no celular e gerenciar avisos.',
                ],
                'emails' => [
                    'nome' => 'Templates de E-mail',
                    'descricao' => 'Personalizar modelos de e-mails transacionais e envio de testes.',
                ],
            ],
        ],
        'sistema_seguranca' => [
            'titulo' => 'Sistema & Segurança',
            'permissoes' => [
                'usuarios' => [
                    'nome' => 'Usuários & Permissões',
                    'descricao' => 'Cadastrar operadores, definir perfis e controlar acessos ao sistema.',
                ],
                'configuracoes' => [
                    'nome' => 'Configurações & SMTP',
                    'descricao' => 'Gerenciar dados da empresa, servidor SMTP e Geo-bloqueio das Américas.',
                ],
                'cron' => [
                    'nome' => 'Central de Cron Jobs',
                    'descricao' => 'Monitorar rotinas agendadas automáticas e executar tarefas sob demanda.',
                ],
                'manutencao' => [
                    'nome' => 'Modo Manutenção',
                    'descricao' => 'Ativar bloqueio temporário do site e configurar tela de aviso.',
                ],
            ],
        ],
    ];

    /**
     * Perfis pré-definidos com conjuntos recomendados de permissões
     */
    public const PAPEIS = [
        'admin' => [
            'nome' => 'Super Administrador',
            'badge' => 'danger',
            'descricao' => 'Acesso total e irrestrito a todas as funcionalidades do sistema.',
            'permissoes_padrao' => ['*'], // Todas
        ],
        'gerente' => [
            'nome' => 'Gerente Geral',
            'badge' => 'primary',
            'descricao' => 'Gestão operacional, clientes, faturas, agendamentos e equipe.',
            'permissoes_padrao' => [
                'dashboard', 'agendamentos', 'estetica', 'planos',
                'clientes', 'faturas', 'gateways', 'cms', 'filiais',
                'parceiros', 'depoimentos', 'notificacoes', 'emails', 'usuarios'
            ],
        ],
        'atendente' => [
            'nome' => 'Atendente / Suporte',
            'badge' => 'info',
            'descricao' => 'Gestão de agendamentos, clientes, notificações e depoimentos.',
            'permissoes_padrao' => [
                'dashboard', 'agendamentos', 'clientes', 'notificacoes', 'depoimentos', 'filiais'
            ],
        ],
        'financeiro' => [
            'nome' => 'Analista Financeiro',
            'badge' => 'success',
            'descricao' => 'Gestão de faturas, clientes, carnês e gateways de pagamento.',
            'permissoes_padrao' => [
                'dashboard', 'clientes', 'faturas', 'gateways', 'notificacoes'
            ],
        ],
        'cliente' => [
            'nome' => 'Cliente / Assinante',
            'badge' => 'secondary',
            'descricao' => 'Acesso restrito à área do cliente (faturas e perfil).',
            'permissoes_padrao' => [],
        ],
    ];

    /**
     * Retorna todas as chaves de permissão possíveis em um array simples
     */
    public static function todasPermissoes(): array
    {
        $todas = [];
        foreach (self::MODULOS as $categoria) {
            foreach ($categoria['permissoes'] as $chave => $dados) {
                $todas[] = $chave;
            }
        }
        return $todas;
    }
}
