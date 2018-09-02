<?php
/*
 * iDimensionz/{doctrine-entity-generator}
 * UnitTestDataProviderTrait.php
 *  
 * The MIT License (MIT)
 * 
 * Copyright (c) 2018 Dimensionz
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

namespace iDimensionz\Tests;

trait UnitTestDataProviderTrait
{
    /**
     * @return array
     */
    public function stringProvider()
    {
        return [
            ['letters and spaces', 'letters and spaces'],
            ['letter and spaces and numbers 1 2 345', 'letter and spaces and numbers 1 2 345'],
            [
                'special characters ! @ # $ % ^ & * ( ) [ ] + - = _ | \ ',
                'special characters ! @ # $ % ^ & * ( ) [ ] + - = _ | \ '
            ],
            ['1234567890', '1234567890'],
            ['null', 'null'],
            [0, '0'],
            [1.23, '1.23'],
            [true, '1'],
            [false, ''],
        ];
    }

    /**
     * @return array
     */
    public function stringProviderWithNull()
    {
        $data = $this->stringProvider();
        $data[] = [null, null];

        return $data;
    }

    /**
     * @return array
     */
    public function booleanProvider()
    {
        return [
            [true, true],
            [false, false],
            ['true', true],
            ['false', true],
            [1, true],
            [0, false],
            ['1', true],
            ['0', false],
            ['', false],
            [456, true],
            ['some string', true],
        ];
    }

    /**
     * @return array
     */
    public function booleanProviderWithNull()
    {
        $data = $this->booleanProvider();
        $data[] = [null, null];

        return $data;
    }

    /**
     * @return array
     */
    public function integerProvider()
    {
        return [
            [0, 0],
            [123, 123],
            [-123, -123],
            [4.56, 4],
            [1.23, 1],
            ['789', 789],
            [true, 1],
            [false, 0]
        ];
    }

    /**
     * @return array
     */
    public function integerProviderWithNull()
    {
        return [
            [null, null],
        ];
    }
}
