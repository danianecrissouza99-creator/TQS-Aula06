# Aula 6 - Testes Unitários com PHP (II)

Enunciado da componente prática, tal como está no PDF da aula (Professor André Silva).

A aula continua a suite de testes escrita na Aula 5 e responde a duas perguntas:

- **Quanto do código está testado?** A cobertura de código mede que parte do código é executada pelos testes.
- **Como testar código que depende de coisas que não controlamos?** Os test doubles e o mocking permitem testar código que depende de APIs, bases de dados ou ficheiros.

## P6.1 - Medir a cobertura (35 min, individual)

Medir a cobertura da suite escrita na Aula 5. Gerar um relatório HTML e identificar as linhas não cobertas.

```
# ativar o motor de cobertura (uma vez)
pecl install pcov             # ou ativar pcov no php.ini

# medir e mostrar na consola
php phpunit --coverage-text

# gerar relatorio HTML navegavel
php phpunit --coverage-html cobertura
```

**Tarefa:** medir a cobertura da suite atual. Abrir o relatório HTML (`cobertura/index.html`) no navegador. Identificar pelo menos uma linha não coberta e explicar porquê.

## P6.2 - Testar uma API com mock (50 min, individual)

Testar um método que consome uma API REST externa, substituindo a chamada de rede por um mock e validando também o tratamento de erros.

Ponto de partida:

```php
public function testCambioComRespostaValida(): void
{
    // cria o cliente HTTP falso (sem rede)
    $http = $this->createStub(ClienteHttp::class);
    // programa a resposta fixa que ele devolve
    $http->method('get')->willReturn(
        new Resposta('{"valor": 1.10}'));

    $loja = new Loja($http);     // injeta o falso
    // verifica so a logica: 100 x 1.10 = 110.0
    $this->assertSame(110.0, $loja->precoComCambio(100));
}
```

**Tarefa:** escrever três testes: resposta válida da API (com stub); erro HTTP (o duplo lança uma exceção e o método deve tratá-la); e resposta com dados em falta. Confirmar que nenhum teste faz pedidos de rede reais.

## P6.3 - Aumentar a cobertura (35 min, individual)

Com base no relatório de P6.1, escrever os testes em falta para elevar a cobertura da camada de domínio a um mínimo de 80%.

1. **Identificar lacunas:** ler o relatório HTML e listar as linhas ainda por cobrir.
2. **Escrever os testes:** para cada lacuna, escrever o teste que a cobre. Muitas serão casos de erro e valores fronteira.
3. **Medir de novo:** voltar a correr a cobertura e confirmar que atingiu 80% ou mais.
4. **Justificar o resto:** documentar as linhas que ficam deliberadamente por cobrir e explicar porquê.

**Entregável:** relatório de cobertura com 80% ou mais na camada de domínio, e um ficheiro breve a justificar cada linha deliberadamente não coberta. Submetido no repositório da UC.
