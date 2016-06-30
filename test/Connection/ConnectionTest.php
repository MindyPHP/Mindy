<?php

use Mindy\Query\Connection;
use Mindy\Query\Transaction;


/**
 * @group db
 * @group mysql
 */
class ConnectionTest extends DatabaseTestCase
{
    public function testConstruct()
    {
        $connection = $this->getConnection(false);
        $params = $this->database;
        $this->assertEquals($params['dsn'], $connection->dsn);
        $this->assertEquals($params['username'], $connection->username);
        $this->assertEquals($params['password'], $connection->password);
    }

    public function testOpenClose()
    {
        $connection = $this->getConnection(false, false);
        $this->assertFalse($connection->isActive);
        $this->assertEquals(null, $connection->pdo);
        $connection->open();
        $this->assertTrue($connection->isActive);
        $this->assertTrue($connection->pdo instanceof \PDO);
        $connection->close();
        $this->assertFalse($connection->isActive);
        $this->assertEquals(null, $connection->pdo);
        $connection = new Connection;
        $connection->dsn = 'unknown::memory:';
        $this->setExpectedException('Mindy\Query\Exception');
        $connection->open();
    }

    public function testGetDriverName()
    {
        $connection = $this->getConnection(false, false);
        $this->assertEquals($this->driverName, $connection->driverName);
    }

    public function testTransaction()
    {
        $connection = $this->getConnection(false);
        $this->assertNull($connection->transaction);
        $transaction = $connection->beginTransaction();
        $this->assertNotNull($connection->getTransaction());
        $this->assertTrue($transaction->getIsActive());

        $connection->createCommand("INSERT INTO profile (description) VALUES ('test transaction')")->execute();
        $transaction->rollBack();
        $this->assertFalse($transaction->getIsActive());
        $this->assertNull($connection->getTransaction());
        $this->assertEquals(0, $connection->createCommand("SELECT COUNT(*) FROM profile WHERE description = 'test transaction';")->queryScalar());
        $transaction = $connection->beginTransaction();
        $connection->createCommand("INSERT INTO profile (description) VALUES ('test transaction')")->execute();
        $transaction->commit();
        $this->assertFalse($transaction->getIsActive());
        $this->assertNull($connection->getTransaction());
        $this->assertEquals(1, $connection->createCommand("SELECT COUNT(*) FROM profile WHERE description = 'test transaction';")->queryScalar());
    }

    public function testTransactionIsolation()
    {
        $connection = $this->getConnection(true);
        $transaction = $connection->beginTransaction(Transaction::READ_UNCOMMITTED);
        $transaction->commit();
        $transaction = $connection->beginTransaction(Transaction::READ_COMMITTED);
        $transaction->commit();
        $transaction = $connection->beginTransaction(Transaction::REPEATABLE_READ);
        $transaction->commit();
        $transaction = $connection->beginTransaction(Transaction::SERIALIZABLE);
        $transaction->commit();
    }

    /**
     * @expectedException \Exception
     */
    public function testTransactionShortcutException()
    {
        $connection = $this->getConnection(true);
        $connection->transaction(function () use ($connection) {
            $connection->createCommand()->insert('profile', ['description' => 'test transaction shortcut'])->execute();
            throw new \Exception('Exception in transaction shortcut');
        });
        $profilesCount = $connection->createCommand("SELECT COUNT(*) FROM profile WHERE description = 'test transaction shortcut';")->queryScalar();
        $this->assertEquals(0, $profilesCount, 'profile should not be inserted in transaction shortcut');
    }

    public function testTransactionShortcutCorrect()
    {
        $connection = $this->getConnection(true);
        $result = $connection->transaction(function () use ($connection) {
            $connection->createCommand("INSERT INTO profile (description) VALUES ('test transaction shortcut')")->execute();
            return true;
        });
        $this->assertTrue($result, 'transaction shortcut valid value should be returned from callback');
        $profilesCount = $connection->createCommand("SELECT COUNT(*) FROM profile WHERE description = 'test transaction shortcut';")->queryScalar();
        $this->assertEquals(1, $profilesCount, 'profile should be inserted in transaction shortcut');
    }

    public function testTransactionShortcutCustom()
    {
        $connection = $this->getConnection(true);
        $result = $connection->transaction(function (Connection $db) {
            $db->createCommand("INSERT INTO profile (description) VALUES ('test transaction shortcut')")->execute();
            return true;
        }, Transaction::READ_UNCOMMITTED);
        $this->assertTrue($result, 'transaction shortcut valid value should be returned from callback');
        $profilesCount = $connection->createCommand("SELECT COUNT(*) FROM profile WHERE description = 'test transaction shortcut';")->queryScalar();
        $this->assertEquals(1, $profilesCount, 'profile should be inserted in transaction shortcut');
    }
}
