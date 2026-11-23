<?php

declare(strict_types=1);

/**
 * Resposta de um pedido HTTP (exercício P6.2).
 *
 * Guarda apenas o corpo da resposta (normalmente um texto em JSON).
 */
class Resposta
{
    public function __construct(private string $corpo)
    {
    }

    /**
     * Devolve o corpo da resposta, tal como veio da API.
     */
    public function getBody(): string
    {
        return $this->corpo;
    }
}
