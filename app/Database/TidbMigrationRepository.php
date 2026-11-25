<?php

namespace App\Database;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class TidbMigrationRepository extends DatabaseMigrationRepository
{
    /**
     * TiDB imports may have an id column without AUTO_INCREMENT.
     * Laravel's default repository omits the id when recording migrations.
     */
    public function log($file, $batch)
    {
        $nextId = ((int) $this->table()->max('id')) + 1;

        $this->table()->insert([
            'id' => $nextId,
            'migration' => $file,
            'batch' => $batch,
        ]);
    }
}
