<?php
/*
 * iDimensionz/{entity-generator-bundle}
 * GenerateEntityCommandTestStub.php
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

namespace iDimensionz\EntityGeneratorBundle\Tests\Command;

use iDimensionz\EntityGeneratorBundle\Command\GenerateEntityCommand;
use iDimensionz\EntityGeneratorBundle\Service\EntityCreatorService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEntityCommandTestStub extends GenerateEntityCommand
{
    public function configure()
    {
        parent::configure();
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
    }

    public function getEntityCreatorService(): EntityCreatorService
    {
        return parent::getEntityCreatorService();
    }
}
