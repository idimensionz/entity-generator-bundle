<?php
/*
 * iDimensionz/{doctrine-entity-generator}
 * EntityCreatorServiceTestStub.php
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

namespace iDimensionz\Tests\Service;

use iDimensionz\Model\EntityPropertyModel;
use iDimensionz\Provider\ColumnDefinitionProviderInterface;
use iDimensionz\Service\EntityCreatorService;

class EntityCreatorServiceTestStub extends EntityCreatorService
{
    public function getColumnDefinitionProvider(): ColumnDefinitionProviderInterface
    {
        return parent::getColumnDefinitionProvider();
    }

    public function getEntityProperties(): array
    {
        return parent::getEntityProperties();
    }

    public function getTwig(): \Twig_Environment
    {
        return parent::getTwig();
    }

    public function getEntityPropertyModel(): EntityPropertyModel
    {
        return parent::getEntityPropertyModel();
    }
}