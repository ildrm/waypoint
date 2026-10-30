<?php

namespace App\Database;

use Illuminate\Database\SQLiteConnection;
use PDOException;

final class SerializingSQLiteConnection extends SQLiteConnection
{
    protected function executeBeginTransactionStatement()
    {
        parent::executeBeginTransactionStatement();

        if (PHP_VERSION_ID >= 80400 || $this->getConfig('transaction_mode') !== 'IMMEDIATE') {
            return;
        }

        // PHP <8.4 cannot reliably track a manually executed BEGIN IMMEDIATE.
        // Reserve the writer immediately inside PDO's tracked transaction instead.
        try {
            $this->getPdo()->exec('UPDATE business_locks SET id = id WHERE id = 1');
        } catch (PDOException $error) {
            // Initial schema creation precedes the mutex migration. Runtime
            // business operations still require BusinessLock's existing row.
            if (str_contains($error->getMessage(), 'no such table: business_locks')) {
                return;
            }
            if ($this->getPdo()->inTransaction()) {
                $this->getPdo()->rollBack();
            }
            throw $error;
        }
    }
}
