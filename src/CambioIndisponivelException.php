<?php

declare(strict_types=1);

/**
 * Exceção lançada pela Loja quando não consegue obter a taxa de câmbio
 * (erro HTTP ou resposta sem dados válidos).
 */
class CambioIndisponivelException extends RuntimeException
{
}
