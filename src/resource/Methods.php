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

namespace cardgate\api\resource {

    use cardgate\api\Exception;
    use cardgate\api\Method;
    use ReflectionClass;
    use ReflectionException;

    /**
     * CardGate resource object.
     */
    final class Methods extends Base
    {
        /**
         * This method can be used to receive a {@link Method} instance.
         *
         * @param string $id Method id to receive method instance for.
         *
         * @return Method
         * @throws Exception|ReflectionException
         * @access public
         * @api
         */
        public function get( string $id): Method
        {
            return new Method($this->client, $id, $id);
        }

        /**
         * This method can be used to retrieve a list of all available payment methods for a site.
         *
         * @param int $siteId The site to retrieve payment methods for.
         *
         * @return array
         * @throws Exception|ReflectionException
         * @access public
         * @api
         */
        public function all( int $siteId): array
        {
            if (! is_integer($siteId)) {
                throw new Exception('Methods.SiteId.Invalid', 'invalid site id: ' . $siteId);
            }

            $resource = "options/{$siteId}/";

            $result = $this->client->doRequest($resource, null, 'GET');

            if (empty($result['options'])) {
                throw new Exception('Method.Options.Invalid', 'unexpected result: ' . $this->client->getLastResult() . $this->client->getDebugInfo(true, false));
            }

            $validMethods = ( new ReflectionClass('\cardgate\api\Method') )->getConstants();
            $methods      = [];
            foreach ($result['options'] as $option) {
                if (!in_array($option['id'], $validMethods)) {
                    continue;
                }

                try {
                    $methods[] = new Method($this->client, $option['id'], $option['name']);
                } catch ( Exception $exception) {
                    trigger_error( $exception->getMessage() . '. Please update this SDK to the latest version.', E_USER_WARNING);
                }
            }
            return $methods;
        }
    }

}
