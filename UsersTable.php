<?php

class UsersTable {

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn;

    /**
     *
     * @var \Doctrine\DBAL\Platforms\AbstractPlatform
     */
    protected $platform;

    /**
     * Table name which is creating by class
     *
     * @var string
     */
    protected $tableName = 'user_Users';

    /**
     * Schema name in which table should be created if Database platform supprt Schemas
     *
     * @var string
     */
    protected $schemaName = 'public';


    public function __construct($conn)
    {
        $this->conn = $conn;

        $this->platform = $this->conn->getDatabasePlatform();
    }

    public function getTableName($quoted = false)
    {
        $tableName = ($this->schemaName && $this->platform->supportsSchemas() ? $this->schemaName . '.' : '') . $this->tableName;

        return ($quoted) ? $this->quote($tableName) : $tableName;
    }

    /**
     * Check if table exists
     *
     * @return boolean
     */
    public function exists()
    {
        $tables = $this->conn->getSchemaManager()->listTableNames();

        return in_array($this->tableName, $tables) || in_array($this->quote($this->tableName), $tables);
    }

    /**
     * Escape identifier
     *
     * @return string
     */
    protected function quote(string $identifier)
    {
        return $this->platform->quoteIdentifier($identifier);
    }

    public function create()
    {
        $this->pr('Create table: ' . $this->tableName);

        $schema = new \Doctrine\DBAL\Schema\Schema();

        $table = $this->prepare($schema);

        $tableCreateSql = $schema->toSql($this->conn->getDatabasePlatform()); // get queries to create this schema.

        $commitTranscation = true;

        $this->conn->beginTransaction();

        foreach ($tableCreateSql as $sql) {
            try {
                $this->conn->query($sql);
            } catch (\Exception $ex) {
                $commitTranscation = false;

                $this->conn->rollBack();

                $this->pr($ex->getMessage());

            }
        }

        if ($commitTranscation) {
            $this->conn->commit();
        }
    }

    /**
     * Methods which adding nesesary columns to table
     *
     */
    protected function prepare($schema)
    {
        $usersTable = $schema->createTable($this->getTableName(true));

        $usersTable->addColumn($this->quote("user_ID"), "integer", array("unsigned" => true));
        $usersTable->addColumn($this->quote("user_Login"), "string", array("length" => 32));
        $usersTable->addColumn($this->quote("user_Name"), "string", array("length" => 100));
        $usersTable->addColumn($this->quote("user_Email"), "string", array("length" => 100));
        $usersTable->addColumn($this->quote("user_Password"), "string", array("length" => 100));
        $usersTable->addColumn($this->quote("created_at"), "datetime");
        $usersTable->addColumn($this->quote("updated_at"), "datetime");

        $usersTable->setPrimaryKey(array($this->quote("user_ID")));
        $usersTable->addUniqueIndex(array($this->quote("user_Login")));
        $usersTable->addIndex(array($this->quote("user_Name")));
        $usersTable->addIndex(array($this->quote("user_Email")));

        $usersTable->setComment('Some comment');

        return $usersTable;
    }

    /**
     * Method which retrive Table Details
     *
     * @param boolean $quote
     * @return \Doctrine\DBAL\Schema\Table
     */
    public function details($quote = false)
    {
        return $this->conn->getSchemaManager()->listTableDetails($this->getTableName($quote));
    }

    public static function pr($data)
    {
        echo '<pre style="text-align: left;">';
        print_r($data);
        echo '</pre>';
    }
}