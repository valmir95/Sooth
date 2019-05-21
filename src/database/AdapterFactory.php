<?php
/* Sooth
 *
 * (The MIT license)
 * Copyright (c) 2019 Valmir Memeti
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated * documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
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
class AdapterFactory{

    /**
     * An associative array of
     */
    const REGISTERED_ADAPTERS = [
        "mysql" => "MySqlAdapter",
    ];


    /**
     * Gets adapter based on adapter-name
     *
     * @param string $className
     * @param Config $config
     * @return DatabaseAdapter|null
     */
    public static function getAdapter($config){
        if(is_null($config)) return null;
        if(empty(self::REGISTERED_ADAPTERS[$config->getAdapter()])){
            throw new Exception($config->getAdapter() . " is not supported.");
        }
        $adapterClassName = self::REGISTERED_ADAPTERS[$config->getAdapter()];

        return new $adapterClassName($config);
    }
}