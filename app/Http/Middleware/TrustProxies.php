<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Nessun proxy fidato di default: senza saperlo dall'hosting, dichiarare
     * qui una topologia e' solo una supposizione. Se in produzione un
     * reverse proxy o una CDN sta davanti all'app (verificalo prima del
     * deploy, vedi Project Doc Step 0), imposta TRUSTED_PROXIES in .env con
     * gli IP/CIDR di quel proxy, oppure '*' se la topologia lo garantisce
     * (es. proxy sullo stesso host, non raggiungibile dall'esterno).
     * Senza TRUSTED_PROXIES nessun header X-Forwarded-* viene fidato, come
     * accadeva prima di questa modifica.
     */
    public function __construct()
    {
        $trusted = trim((string) env('TRUSTED_PROXIES', ''));

        $this->proxies = match (true) {
            $trusted === '' => null,
            $trusted === '*' => '*',
            default => array_filter(array_map('trim', explode(',', $trusted))),
        };
    }
}
