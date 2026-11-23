# TQS - Aula 6: Testes Unitários com PHP (II)

Exercícios práticos da Aula 6 de Testes e Qualidade de Software (ISTEC, CTeSP em Desenvolvimento de Software, 2.º ano).

Daniane Christyni Souza da Silva, n.º 2025055
Docente: André Silva

Esta aula continua o projeto da Aula 5. O objetivo era medir a cobertura de código da suite, testar uma classe que depende de uma API externa usando test doubles (stub e mock), e chegar a pelo menos 80% de cobertura na camada de domínio.

No fim fiquei com 33 testes a passar e 97,22% de cobertura, com a camada de domínio a 100%.

O enunciado das tarefas está no ficheiro `ENUNCIADO.md` e a justificação da cobertura está no `COBERTURA.md`.

## Estrutura

| Ficheiro | O que tem |
| --- | --- |
| `src/Carrinho.php` | A classe do carrinho (da Aula 5) |
| `src/ValidadorPasse.php` | O validador de palavra-passe (da Aula 5) |
| `src/Loja.php` | Converte preços de euros para dólares com a taxa de uma API (novo, P6.2) |
| `src/ClienteHttp.php` | Interface do cliente HTTP, a "ligação à rede" (novo) |
| `src/Resposta.php` | A resposta da API (novo) |
| `src/CambioIndisponivelException.php` | Erro lançado quando a API falha ou não devolve a taxa (novo) |
| `tests/CarrinhoTest.php` | Testes do Carrinho (da Aula 5) |
| `tests/ValidadorPasseTest.php` | Testes do validador (da Aula 5) |
| `tests/LojaTest.php` | Testes da Loja com stubs e mock (novo, P6.2) |
| `cobertura/` | Relatório de cobertura em HTML (abrir `cobertura/index.html`) |
| `COBERTURA.md` | Resultado da cobertura e justificação da linha não coberta |

## Preparar e correr

O PHPUnit não está no repositório, por isso é preciso descarregá-lo para a pasta do projeto:

```
php -r "copy('https://phar.phpunit.de/phpunit-11.phar', 'phpunit');"
```

Para correr os testes:

```
php phpunit --testdox tests
```

Para medir a cobertura é preciso um motor de cobertura no PHP. Eu usei o Xdebug 3.5, com `xdebug.mode=coverage` no `php.ini`. Depois:

```
php phpunit --coverage-filter src --coverage-text tests            # na consola
php phpunit --coverage-filter src --coverage-html cobertura tests  # relatório HTML
```

Como o projeto ainda não tem `phpunit.xml`, é preciso indicar a pasta a medir com `--coverage-filter src`.

## P6.1 - Medir a cobertura

**O que era pedido:** medir a cobertura da suite da Aula 5, gerar o relatório HTML e identificar pelo menos uma linha não coberta, explicando porquê.

**O que fiz:** com os testes da Aula 5, o `Carrinho` e o `ValidadorPasse` já estavam a 100%, por isso não havia linhas por cobrir. Para ter um caso real, criei primeiro a classe `Loja` do P6.2 e medi a cobertura antes de escrever os testes dela:

| Ficheiro | Cobertura | Porquê |
| --- | --- | --- |
| `Carrinho.php` | 100% | Coberto pelos testes da Aula 5 |
| `ValidadorPasse.php` | 100% | Coberto pelos testes da Aula 5 |
| `Loja.php` | 0% | Ainda nenhum teste a usava |
| `Resposta.php` | 0% | Só é usada pela Loja |
| **Total** | **53,85%** | 21 de 39 linhas |

## P6.2 - Testar uma API com mock

**O que era pedido:** testar um método que usa uma API REST externa sem fazer pedidos de rede, substituindo a chamada por um test double. Eram pedidos três testes: resposta válida, erro HTTP e resposta com dados em falta.

**O que fiz:** o enunciado só mostrava o teste, por isso criei as classes `Loja`, `ClienteHttp` e `Resposta` para encaixar no exemplo do slide. O cliente HTTP entra na `Loja` pelo construtor, e é isso que permite trocá-lo por um falso nos testes.

Quando a API falha, ou a resposta não traz uma taxa válida, a `Loja` trata o problema e lança uma `CambioIndisponivelException` com uma mensagem clara.

| Teste | Double usado | O que verifica |
| --- | --- | --- |
| Resposta válida | Stub | 100 € com taxa 1,10 dá 110 $ |
| Erro HTTP | Stub que lança exceção | A Loja trata o erro e lança `CambioIndisponivelException` |
| Resposta inválida (Data Provider, 4 casos) | Stub | Campo `valor` em falta, resposta vazia, valor não numérico e JSON inválido |
| Pede a taxa uma vez | Mock | A Loja chama o `get()` uma só vez e ao URL certo |

Nenhum destes testes faz pedidos de rede: o `ClienteHttp` é sempre falso.

O exemplo do slide usa `assertSame(110.0, ...)`, mas em PHP 100 x 1,10 dá `110.00000000000001`, por isso esse teste falharia. Usei `assertEqualsWithDelta`, que aceita uma diferença mínima, como se recomenda para valores decimais calculados. No teste do mock usei a taxa 1,25 (50 x 1,25 = 62,5, um valor exato), por isso aí o `assertSame` funciona.

## P6.3 - Aumentar a cobertura

**O que era pedido:** chegar a pelo menos 80% de cobertura na camada de domínio e justificar por escrito as linhas deliberadamente não cobertas.

**O que fiz:** depois de escrever o `LojaTest.php`, voltei a medir:

| Ficheiro | Antes | Depois |
| --- | --- | --- |
| `Carrinho.php` | 100% | 100% |
| `ValidadorPasse.php` | 100% | 100% |
| `Loja.php` | 0% | 100% |
| `Resposta.php` | 0% | 100% |
| `ClienteHttp.php` | 0% | 0% (1 linha, justificada) |
| **Total** | **53,85%** | **97,22%** |

A única linha por cobrir é o `require_once` do `ClienteHttp.php`. Corre quando o PHPUnit carrega os testes, antes de começar a medir, e não é lógica do programa. A explicação completa está no `COBERTURA.md`.

## Resultado

Com `php phpunit --testdox tests` passam os 33 testes:

| Ficheiro | Testes |
| --- | --- |
| `CarrinhoTest.php` | 19 |
| `ValidadorPasseTest.php` | 7 |
| `LojaTest.php` | 7 |
| **Total** | **33** |

## O que aprendi

Ver a cobertura descer para 53% quando acrescentei uma classe sem testes mostrou bem para que serve o relatório: aponta logo o código que ninguém está a verificar.

Também percebi que cobertura alta não chega. O que dá valor a um teste é o assert. Por isso tive cuidado em verificar sempre o resultado, os erros e os valores-limite, e não apenas em fazer as linhas correr.

Com os stubs e o mock consegui testar a lógica da `Loja` sem depender da internet. Os testes ficaram rápidos e dão sempre o mesmo resultado.
