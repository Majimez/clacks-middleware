<?php
/**
 * Copyright (c) 2017-2018 Martin Meredith
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace Clacks;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class OverheadMiddleware
 *
 * @package Clacks
 */
class OverheadMiddleware implements MiddlewareInterface
{
    public const HEADER = 'X-Clacks-Overhead';

    /**
     * Process an incoming server request and return a response, adding in
     * X-Clacks-Overhead headers
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $returnCommands = ['GNU Terry Pratchett'];

        if ($request->hasHeader(self::HEADER)) {
            $returnCommands = $this->processCommands($request);
        }

        $request = $request->withHeader(self::HEADER, $returnCommands);

        $response = $handler->handle($request);

        return $response->withHeader(self::HEADER, $returnCommands);
    }

    /**
     * processCommands
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return array
     */
    private function processCommands(ServerRequestInterface $request): array
    {
        $returnCommands = [];

        $commands = $request->getHeader(self::HEADER);

        foreach ($commands as $command) {
            $retval = $this->processCommand($command);

            if (!is_null($retval)) {
                $returnCommands[] = $retval;
            }
        }

        return array_unique($returnCommands);
    }

    /**
     * processCommand
     *
     * @param $command
     *
     * @return array|null
     */
    private function processCommand($command): ?array
    {
        $command = trim($command);

        if (strlen($command) > 64) {
            return null;
        }

        if (preg_match('/^GNU /', $command)) {
            return $command;
        }

        return null;
    }
}
