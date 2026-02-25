<?php

/**
 * Copyright (c) 2018 CardGate B.V.
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
 *
 * @license     The MIT License (MIT) https://opensource.org/licenses/MIT
 * @author      CardGate B.V.
 * @copyright   CardGate B.V.
 * @link        https://www.cardgate.com
 */

namespace cardgate\api;

/**
 * Class for all exceptions specific to the CardGate client library.
 */
final class Exception extends \Exception
{
    /**
     * The unified string code of the exception.
     * @var string
     * @access private
     */
    private $error;

    /**
     * Constructs the exception.
     * @param string $error A unified string code of the exception to throw.
     * @param string $message The exception message to throw.
     * @param int $code The numeric exception code.
     * @param \Throwable|null $previous The previous exception used for the exception chaining.
     * @access public
     * @api
     */
    public function __construct(string $error, string $message, int $code = 0, ?\Throwable $previous = null)
    {
        $this->error = $error;
        $message = 'CL: '.$message;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the unified string code associated with this exception.
     * @return string The unified string code of the exception.
     * @access public
     * @api
     */
    public function getError(): string
    {
        return $this->error;
    }
}
