<?php


use ChameleonSystem\CoreBundle\ServiceLocator;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Doctrine\DBAL\Connection;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Doctrine\DBAL\Driver\Statement;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorderStateHandler;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorder;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\Callback;
use Symfony\Component\DependencyInjection\Container;

require __DIR__.'/DependencyInjectionContainerMock.php';

class TCMSTableEditorEndPointTest extends TestCase
{

    /** @var TCMSTableEditorEndPoint */
    protected $subject;

    /** @var MockObject<Connection> */
    protected $db;

    /** @var MockObject<MigrationRecorderStateHandler> */
    protected $stateHandler;

    /** @var MockObject<MigrationRecorder> */
    protected $migrationRecorder;

    public function setUp(): void
    {
        parent::setUp();
        $this->db = $this->createMock(Connection::class);
        $this->db->method('quote')->willReturnCallback(function ($content) {
            return $content;
        });
        $this->db->method('quoteIdentifier')->willReturnCallback(function ($content) {
            return sprintf('`%s`', $content);
        });

        $this->stateHandler = $this->createMock(MigrationRecorderStateHandler::class);
        $this->migrationRecorder = $this->createMock(MigrationRecorder::class);

        $serviceContainer = new DependencyInjectionContainerMock();
        $serviceContainer->services['chameleon_system_core.language_service'] = $this->createMock(
            LanguageServiceInterface::class
        );
        $serviceContainer->services['chameleon_system_core.language_service']->method(
            'getActiveEditLanguage'
        )->willReturn((object)['fieldIso6391' => 'de']);
        $serviceContainer->services['database_connection'] = $this->db;
        $serviceContainer->services['chameleon_system_core.cache'] = $this->createMock(
            \esono\pkgCmsCache\CacheInterface::class
        );
        $serviceContainer->services['chameleon_system_database_migration.recorder.migration_recorder_state_handler'] = $this->stateHandler;
        $serviceContainer->services['chameleon_system_database_migration.recorder.migration_recorder'] = $this->migrationRecorder;
        ServiceLocator::setContainer($serviceContainer);
    }

    public function tearDown(): void
    {
        ServiceLocator::setContainer(new Container());
        MySqlLegacySupport::resetInstance();
        parent::tearDown();
    }

    public function testDoesNotInsertAnythingIfMltTableAlreadyContainsMatches(): void
    {
        $this->expectQueries(
            [
                // 1 match = record in MLT table already exists
                [
                    '/SELECT COUNT\\(\\*\\) .* FROM `mlt_table` .* `source_id` = \'123\' .* `target_id` = \'456\'/',
                    ['cmsmatches' => 1],
                ],
            ]
        );

        $field = $this->getMockedMltField('connected_table', 'mlt_table');
        $tableEditor = $this->initializeEditor('123');
        $this->callProtectedMethod($tableEditor, 'AddMLTConnectionExecute', [$field, '456']);
    }

    public function testInsertsIntoMltTableIfNotExistsYet(): void
    {
        $this->expectQueries(
            [
                [
                    // 0 matches = record in MLT table does not exist
                    '/SELECT COUNT\\(\\*\\) .* FROM `mlt_table` .* `source_id` = \'123\' .* `target_id` = \'456\'/',
                    ['cmsmatches' => 0],
                ],
                [
                    '/SELECT `entry_sort`/',
                    ['entry_sort' => 0],
                ],
                [
                    '/INSERT INTO `mlt_table` SET `source_id` = \'123\', `target_id` = \'456\'/',
                    [],
                ],
            ]
        );

        $field = $this->getMockedMltField('connected_table', 'mlt_table');
        $tableEditor = $this->initializeEditor('123');
        $this->callProtectedMethod($tableEditor, 'AddMLTConnectionExecute', [$field, '456']);
    }

    public function testRecordsTableChangeIfDatabaseListenerIsActive(): void
    {
        $this->expectQueries(
            [
                [
                    // 0 matches = record in MLT table does not exist
                    '/SELECT COUNT\\(\\*\\) .* FROM `mlt_table` .* `source_id` = \'123\' .* `target_id` = \'456\'/',
                    ['cmsmatches' => 0],
                ],
                [
                    '/SELECT `entry_sort`/',
                    ['entry_sort' => 0],
                ],
                [
                    '/INSERT INTO `mlt_table` SET `source_id` = \'123\', `target_id` = \'456\'/',
                    [],
                ],
            ]
        );

        $this->stateHandler->method('isDatabaseLoggingActive')->willReturn(true);
        $this->migrationRecorder
            ->expects($this->once())
            ->method('writeQueries')
            ->with(null, new Callback(
                function (array $changes) {
                    /** @var LogChangeDataModel[] $changes */
                    return count($changes) === 1
                        && $changes[0]->getType() === 'insert'
                        && $changes[0]->getData()->getTableName() === 'mlt_table'
                        && $changes[0]->getData()->getFields()['source_id'] === '123'
                        && $changes[0]->getData()->getFields()['target_id'] === '456';
                }
            ));

        $field = $this->getMockedMltField('connected_table', 'mlt_table');
        $tableEditor = $this->initializeEditor('123');
        $this->callProtectedMethod($tableEditor, 'AddMLTConnectionExecute', [$field, '456']);
    }

    private function initializeEditor(string $currentId): TCMSTableEditorEndPoint
    {
        $tableEditor = new TCMSTableEditorEndPoint();
        $tableEditor->sId = $currentId;
        $tableEditor->oTableConf = new TCMSTableConf();
        $tableEditor->oTableConf->sqlData = ['name' => 'foo'];

        return $tableEditor;
    }

    private function getMockedMltField(string $connectedTable, string $mltTable): TCMSMLTField
    {
        $field = $this->createMock(TCMSFieldLookupMultiselect::class);
        $field->method('GetConnectedTableName')->willReturn($connectedTable);
        $field->method('GetMLTTableName')->willReturn($mltTable);
        $field->oDefinition = $this->createMock(TCMSFieldDefinition::class);
        $field->oDefinition->method('GetFieldType')->willReturn(new TCMSFieldType());

        return $field;
    }

    private function callProtectedMethod($object, string $method, array $arguments)
    {
        $reflection = new ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    private function expectQueries(array $queries)
    {
        /** @var RegularExpression[] $regexes */
        $regexes = array_map(function ($query) {
            return new RegularExpression($query[0]);
        }, $queries);

        $this->db
            ->expects($this->exactly(count($queries)))
            ->method('query')
            ->with(LogicalOr::fromConstraints(...$regexes))
            ->willReturnCallback(function ($queryThatMatches) use ($regexes, $queries) {
                foreach ($regexes as $i => $regex) {
                    if ($regex->evaluate($queryThatMatches, '', true)) {
                        $result = $this->createMock(Statement::class);
                        $result->method('fetch')->willReturn($queries[$i][1] ?? null);

                        return $result;
                    }
                }

                return null;
            });
    }
}
