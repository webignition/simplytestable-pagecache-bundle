<?php

namespace SimplyTestable\PageCacheBundle\Tests\Functional\Services;

use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;
use SimplyTestable\PageCacheBundle\Command\ClearCommand;
use SimplyTestable\PageCacheBundle\Entity\CacheValidatorHeaders;
use SimplyTestable\PageCacheBundle\Tests\Functional\AbstractFunctionalTestCase;
use SimplyTestable\PageCacheBundle\Tests\Functional\EntityManagerProxy;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ClearCommandTest extends AbstractFunctionalTestCase
{
    /**
     * @var ClearCommand
     */
    private $command;

    protected function setUp()
    {
        parent::setUp();

        $this->command = $this->container->get(ClearCommand::class);
    }

    /**
     * @dataProvider runDataProvider
     *
     * @param array $args
     * @param int $count
     * @param array $expectedOutputLines
     *
     * @throws \Exception
     */
    public function testRun(array $args, int $count, array $expectedOutputLines)
    {
        /* @var EntityManagerProxy $entityManagerProxy */
        $entityManagerProxy = $this->container->get(EntityManagerProxy::class);

        /* @var EntityManagerInterface|MockInterface $entityRepositoryMock */
        $entityRepositoryMock = $entityManagerProxy->getRepository(CacheValidatorHeaders::class);

        $entityRepositoryMock
            ->shouldReceive('count')
            ->andReturn($count, 0);

        if (isset($args['limit'])) {
            $entityRepositoryMock
                ->shouldReceive('findAll')
                ->with($args['limit'])
                ->andReturn([]);
        }

        $input = new ArrayInput($args);
        $output = new BufferedOutput();

        $this->command->run($input, $output);

        $this->assertEquals($expectedOutputLines, explode("\n", $output->fetch()));
    }

    public function runDataProvider(): array
    {
        return [
            'no entities, no defined limit' => [
                'args' => [],
                'count' => 0,
                'expectedOutputLines' => [
                    'Clearing cache validator headers',
                    '0 items to delete',
                    '100 items per batch',
                    '',
                    'Done!',
                    '',
                ],
            ],
            'no entities, has limit' => [
                'args' => [
                    'limit' => 10,
                ],
                'count' => 0,
                'expectedOutputLines' => [
                    'Clearing cache validator headers',
                    '0 items to delete',
                    '10 items per batch',
                    '',
                    'Done!',
                    '',
                ],
            ],
            'has entities, has limit' => [
                'args' => [
                    'limit' => 10,
                ],
                'count' => 10,
                'expectedOutputLines' => [
                    'Clearing cache validator headers',
                    '10 items to delete',
                    '10 items per batch',
                    '',
                    'Deleting up to 10 of 10',
                    'Done!',
                    '',
                ],
            ],
        ];
    }
}
