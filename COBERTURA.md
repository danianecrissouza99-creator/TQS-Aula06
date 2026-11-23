# Cobertura de código - Aula 6

Daniane Christyni Souza da Silva, n.º 2025055

A cobertura foi medida com o PHPUnit 11.5 e o Xdebug 3.5:

```
php phpunit --coverage-filter src --coverage-text tests
php phpunit --coverage-filter src --coverage-html cobertura tests
```

O relatório HTML completo está na pasta `cobertura` (abrir `cobertura/index.html`).

## Resultado

| Ficheiro | Linhas cobertas | Cobertura |
| --- | --- | --- |
| `Carrinho.php` | 14 / 14 | 100% |
| `ValidadorPasse.php` | 7 / 7 | 100% |
| `Loja.php` | 12 / 12 | 100% |
| `Resposta.php` | 2 / 2 | 100% |
| `ClienteHttp.php` | 0 / 1 | 0% |
| `CambioIndisponivelException.php` | sem linhas executáveis | - |
| **Total** | **35 / 36** | **97,22%** |

O objetivo da aula era chegar a pelo menos 80% na camada de domínio. A camada de domínio (`Carrinho`, `ValidadorPasse` e `Loja`) está a 100%.

## Antes e depois

Quando criei a classe `Loja` para o P6.2, medi a cobertura antes de escrever os testes dela. O total desceu para 53,85% (21 de 39 linhas), porque a `Loja` e a `Resposta` estavam a 0%: nenhum teste as usava. Depois de escrever o `LojaTest.php`, a cobertura subiu para 97,22%.

## Linha deliberadamente não coberta

### `ClienteHttp.php`, linha do `require_once`

```php
require_once __DIR__ . '/Resposta.php';
```

É a única linha que o relatório marca como não coberta. Não é lógica do programa: só carrega o ficheiro da classe `Resposta`, que a interface usa no tipo de retorno do `get()`.

Esta linha corre de facto, mas corre quando o PHPUnit carrega os ficheiros de teste, antes de começar a medir a cobertura. Por isso o relatório não a conta. A interface `ClienteHttp` não tem mais nenhuma linha executável, porque só declara o método `get()`, sem código.

Não escrevi nenhum teste para esta linha porque não há comportamento para verificar. Na Aula 7 os `require_once` vão ser substituídos pelo autoload do Composer, e esta linha deixa de existir.

## Observações

- A cobertura alta não quer dizer, por si só, que os testes são bons. Por isso todos os testes têm asserts que comparam o esperado com o obtido, e incluem casos de erro e valores-limite (quantidade 20, preço 0, idades 18 e 25, palavras-passe com 7 e 8 caracteres, respostas inválidas da API).
- Nenhum teste da `Loja` faz pedidos de rede reais: o `ClienteHttp` é sempre substituído por um stub ou por um mock.
