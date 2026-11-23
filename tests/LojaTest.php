<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Loja.php';

/**
 * Testes da classe Loja (exercício P6.2).
 *
 * O cliente HTTP é sempre substituído por um test double (stub ou mock).
 * Por isso NENHUM teste faz pedidos de rede reais: são rápidos, isolados
 * e repetíveis (princípios FIRST).
 */
class LojaTest extends TestCase
{
    // ------------------------------------------------------------------
    // 1. Resposta válida da API (com stub)
    // ------------------------------------------------------------------

    // Ponto de partida do slide: 100 EUR x 1.10 = 110.0 USD.
    // Nota: 100 x 1.10 em vírgula flutuante dá 110.00000000000001, por isso
    // usa-se assertEqualsWithDelta (o slide da Aula 5 recomenda não usar
    // assertSame para floats calculados).
    public function testPrecoComCambio_RespostaValida_AplicaTaxa(): void
    {
        // Arrange: cliente HTTP falso que devolve uma resposta fixa
        $http = $this->createStub(ClienteHttp::class);
        $http->method('get')->willReturn(new Resposta('{"valor": 1.10}'));
        $loja = new Loja($http);

        // Act
        $preco = $loja->precoComCambio(100);

        // Assert
        $this->assertEqualsWithDelta(110.0, $preco, 0.0001);
    }

    // ------------------------------------------------------------------
    // 2. Erro HTTP (o double lança uma exceção e a Loja trata-a)
    // ------------------------------------------------------------------

    public function testPrecoComCambio_ErroHttp_LancaCambioIndisponivel(): void
    {
        // Arrange: o cliente falso simula uma falha da API
        $http = $this->createStub(ClienteHttp::class);
        $http->method('get')->willThrowException(new RuntimeException('HTTP 500'));
        $loja = new Loja($http);

        // Assert (antes do Act, como em todos os testes de exceção)
        $this->expectException(CambioIndisponivelException::class);
        $this->expectExceptionMessage('Nao foi possivel obter a taxa de cambio.');

        // Act
        $loja->precoComCambio(100);
    }

    // ------------------------------------------------------------------
    // 3. Resposta com dados em falta ou inválidos (Data Provider)
    // ------------------------------------------------------------------

    public static function casosRespostaInvalida(): array
    {
        //                          [ corpo da resposta ]
        return [
            'campo valor em falta'  => ['{"moeda": "USD"}'],
            'resposta vazia'        => ['{}'],
            'valor nao numerico'    => ['{"valor": "abc"}'],
            'json invalido'         => ['Erro interno do servidor'],
        ];
    }

    #[DataProvider('casosRespostaInvalida')]
    public function testPrecoComCambio_RespostaInvalida_LancaCambioIndisponivel(string $corpo): void
    {
        // Arrange
        $http = $this->createStub(ClienteHttp::class);
        $http->method('get')->willReturn(new Resposta($corpo));
        $loja = new Loja($http);

        // Assert
        $this->expectException(CambioIndisponivelException::class);
        $this->expectExceptionMessage('A resposta da API nao tem a taxa de cambio.');

        // Act
        $loja->precoComCambio(100);
    }

    // ------------------------------------------------------------------
    // Extra: mock (verifica a interação, não só a resposta)
    // ------------------------------------------------------------------

    // O mock confirma que a Loja pede a taxa uma única vez e ao URL certo.
    // 50 EUR x 1.25 = 62.5 USD (conta exata, por isso assertSame funciona).
    public function testPrecoComCambio_PedeTaxaUmaVezAoUrlCerto(): void
    {
        // Arrange: mock com expectativa de interação
        $http = $this->createMock(ClienteHttp::class);
        $http->expects($this->once())
             ->method('get')
             ->with('https://api.cambio.pt/eur-usd')
             ->willReturn(new Resposta('{"valor": 1.25}'));
        $loja = new Loja($http);

        // Act
        $preco = $loja->precoComCambio(50);

        // Assert
        $this->assertSame(62.5, $preco);
    }
}
