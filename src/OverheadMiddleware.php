<?php

namespace Clacks;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class OverheadMiddleware implements MiddlewareInterface
{
    const HEADER = 'X-Clacks-Overhead';

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $returnCommands = ['GNU Terry Pratchett'];

        if ($request->hasHeader(self::HEADER)) {
            $header = $request->getHeaders()[self::HEADER];

            $commands = explode(',', $header);

            foreach ($commands as $command) {
                $command = trim($command);

                if (preg_match('/^GNU /', $command)) {
                    $returnCommands[] = $command;
                }
            }
        }

        $response = $delegate->process($request);

        return $response->withHeader(self::HEADER, implode(', ', $returnCommands));
    }
}
