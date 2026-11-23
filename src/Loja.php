<?php

declare(strict_types=1);

require_once __DIR__ . '/ClienteHttp.php';
require_once __DIR__ . '/Resposta.php';
require_once __DIR__ . '/CambioIndisponivelException.php';

/**
 * Loja (exercício P6.2).
 *
 * Converte preços de euros para dólares, usando a taxa de câmbio
 * de uma API externa. O cliente HTTP entra pelo construtor
 * (injeção de dependência), para que nos testes possa ser trocado
 * por um stub ou mock, sem rede.
 */
class Loja
{
    private const URL_CAMBIO = 'https://api.cambio.pt/eur-usd';

    public function __construct(private ClienteHttp $http)
    {
    }

    /**
     * Converte um preço em euros para dólares.
     *
     * @throws CambioIndisponivelException se a API falhar ou a resposta não tiver a taxa.
     */
    public function precoComCambio(float $precoEur): float
    {
        // 1. Pede a taxa à API. Se o pedido falhar, a exceção é tratada
        //    e transformada numa exceção da própria Loja, com mensagem clara.
        try {
            $resposta = $this->http->get(self::URL_CAMBIO);
        } catch (RuntimeException $erro) {
            throw new CambioIndisponivelException(
                'Nao foi possivel obter a taxa de cambio.',
                0,
                $erro
            );
        }

        // 2. Lê o corpo da resposta (JSON) e converte para array.
        $dados = json_decode($resposta->getBody(), true);

        // 3. Confirma que a resposta tem uma taxa válida.
        //    (JSON inválido, campo "valor" em falta ou valor não numérico)
        if (!is_array($dados) || !isset($dados['valor']) || !is_numeric($dados['valor'])) {
            throw new CambioIndisponivelException('A resposta da API nao tem a taxa de cambio.');
        }

        // 4. Aplica a taxa ao preço. Esta é a lógica que os testes verificam.
        return $precoEur * (float) $dados['valor'];
    }
}
