<?php

declare(strict_types=1);

require_once __DIR__ . '/Resposta.php';

/**
 * Cliente HTTP (exercício P6.2).
 *
 * É a "fronteira externa" da Loja: na vida real faria pedidos pela rede.
 * Por ser uma interface, nos testes é substituído por um stub ou mock,
 * e nenhum teste faz pedidos de rede reais.
 */
interface ClienteHttp
{
    /**
     * Faz um pedido GET ao URL indicado e devolve a resposta.
     *
     * @throws RuntimeException se o pedido falhar (erro HTTP, sem rede, etc.).
     */
    public function get(string $url): Resposta;
}
