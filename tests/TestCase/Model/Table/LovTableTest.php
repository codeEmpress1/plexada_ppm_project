<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LovTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LovTable Test Case
 */
class LovTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LovTable
     */
    public $Lov;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Lov',
        'app.SystemUsers',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Lov') ? [] : ['className' => LovTable::class];
        $this->Lov = TableRegistry::getTableLocator()->get('Lov', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Lov);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
