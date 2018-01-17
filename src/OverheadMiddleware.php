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
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Clacks;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class OverheadMiddleware
 *
 * @package Clacks
 */
class OverheadMiddleware implements MiddlewareInterface
{
    public const HEADER = 'X-Clacks-Overhead';

    /**
     * Process an incoming server request and return a response, adding in X-Clacks-Overhead headers
     *
     * @param \Psr\Http\Message\ServerRequestInterface|\Psr\Http\Message\MessageInterface $request
     * @param \Interop\Http\ServerMiddleware\DelegateInterface $delegate
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $returnCommands = ['GNU Terry Pratchett'];

        if ($request->hasHeader(self::HEADER)) {
            $returnCommands = $this->processCommands($request);
        }

        $request = $request->withHeader(self::HEADER, $returnCommands);

        $response = $delegate->process($request);

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
            if ($retval = $this->processCommand($command)) {
                $returnCommands[] = $retval;
            }
        }

        return $returnCommands;
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

        if (preg_match('/^GNU /', $command)) {
            return $command;
        }

        return null;
    }
}
