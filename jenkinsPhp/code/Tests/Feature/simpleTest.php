<?php
/**
 * SearchTest
 *
 */

namespace Feature;

require_once __DIR__ . '/../BaseTest.php';



use Tests\BaseTest;

class simpleTest extends BaseTest
{
    public function testAssert(): void
    {
        $this->assertEquals(1,1 );
    }


    public function testDatabase(): void
    {
        $query = 'TRUNCATE TABLE  Test';
        $this->db->executeQuery($query);
        
        $query = 'INSERT INTO  Test (name) VALUES("Test name")';
        $this->db->executeQuery($query);

    
        $result = $this->db->executeSelectQuery('SELECT * FROM Test WHERE name = "Test name"');
        $this->assertEquals("Test name", $result[0]["name"] );

    }


}