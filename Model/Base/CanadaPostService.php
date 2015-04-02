<?php

namespace CanadaPost\Model\Base;

use \Exception;
use \PDO;
use CanadaPost\Model\CanadaPostOrder as ChildCanadaPostOrder;
use CanadaPost\Model\CanadaPostOrderQuery as ChildCanadaPostOrderQuery;
use CanadaPost\Model\CanadaPostService as ChildCanadaPostService;
use CanadaPost\Model\CanadaPostServiceI18n as ChildCanadaPostServiceI18n;
use CanadaPost\Model\CanadaPostServiceI18nQuery as ChildCanadaPostServiceI18nQuery;
use CanadaPost\Model\CanadaPostServiceQuery as ChildCanadaPostServiceQuery;
use CanadaPost\Model\Map\CanadaPostServiceTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;

abstract class CanadaPostService implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\CanadaPost\\Model\\Map\\CanadaPostServiceTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the visible field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $visible;

    /**
     * The value for the code field.
     * @var        string
     */
    protected $code;

    /**
     * @var        ObjectCollection|ChildCanadaPostOrder[] Collection to store aggregation of ChildCanadaPostOrder objects.
     */
    protected $collCanadaPostOrders;
    protected $collCanadaPostOrdersPartial;

    /**
     * @var        ObjectCollection|ChildCanadaPostServiceI18n[] Collection to store aggregation of ChildCanadaPostServiceI18n objects.
     */
    protected $collCanadaPostServiceI18ns;
    protected $collCanadaPostServiceI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';

    /**
     * Current translation objects
     * @var        array[ChildCanadaPostServiceI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $canadaPostOrdersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $canadaPostServiceI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->visible = 0;
    }

    /**
     * Initializes internal state of CanadaPost\Model\Base\CanadaPostService object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>CanadaPostService</code> instance.  If
     * <code>obj</code> is an instance of <code>CanadaPostService</code>, delegates to
     * <code>equals(CanadaPostService)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return CanadaPostService The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return CanadaPostService The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [visible] column value.
     *
     * @return   int
     */
    public function getVisible()
    {

        return $this->visible;
    }

    /**
     * Get the [code] column value.
     *
     * @return   string
     */
    public function getCode()
    {

        return $this->code;
    }

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \CanadaPost\Model\CanadaPostService The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CanadaPostServiceTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [visible] column.
     *
     * @param      int $v new value
     * @return   \CanadaPost\Model\CanadaPostService The current object (for fluent API support)
     */
    public function setVisible($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->visible !== $v) {
            $this->visible = $v;
            $this->modifiedColumns[CanadaPostServiceTableMap::VISIBLE] = true;
        }


        return $this;
    } // setVisible()

    /**
     * Set the value of [code] column.
     *
     * @param      string $v new value
     * @return   \CanadaPost\Model\CanadaPostService The current object (for fluent API support)
     */
    public function setCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->code !== $v) {
            $this->code = $v;
            $this->modifiedColumns[CanadaPostServiceTableMap::CODE] = true;
        }


        return $this;
    } // setCode()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->visible !== 0) {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CanadaPostServiceTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CanadaPostServiceTableMap::translateFieldName('Visible', TableMap::TYPE_PHPNAME, $indexType)];
            $this->visible = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CanadaPostServiceTableMap::translateFieldName('Code', TableMap::TYPE_PHPNAME, $indexType)];
            $this->code = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 3; // 3 = CanadaPostServiceTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \CanadaPost\Model\CanadaPostService object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CanadaPostServiceTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCanadaPostServiceQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCanadaPostOrders = null;

            $this->collCanadaPostServiceI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see CanadaPostService::setDeleted()
     * @see CanadaPostService::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CanadaPostServiceTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildCanadaPostServiceQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CanadaPostServiceTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CanadaPostServiceTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->canadaPostOrdersScheduledForDeletion !== null) {
                if (!$this->canadaPostOrdersScheduledForDeletion->isEmpty()) {
                    foreach ($this->canadaPostOrdersScheduledForDeletion as $canadaPostOrder) {
                        // need to save related object because we set the relation to null
                        $canadaPostOrder->save($con);
                    }
                    $this->canadaPostOrdersScheduledForDeletion = null;
                }
            }

                if ($this->collCanadaPostOrders !== null) {
            foreach ($this->collCanadaPostOrders as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->canadaPostServiceI18nsScheduledForDeletion !== null) {
                if (!$this->canadaPostServiceI18nsScheduledForDeletion->isEmpty()) {
                    \CanadaPost\Model\CanadaPostServiceI18nQuery::create()
                        ->filterByPrimaryKeys($this->canadaPostServiceI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->canadaPostServiceI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collCanadaPostServiceI18ns !== null) {
            foreach ($this->collCanadaPostServiceI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[CanadaPostServiceTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CanadaPostServiceTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CanadaPostServiceTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CanadaPostServiceTableMap::VISIBLE)) {
            $modifiedColumns[':p' . $index++]  = 'VISIBLE';
        }
        if ($this->isColumnModified(CanadaPostServiceTableMap::CODE)) {
            $modifiedColumns[':p' . $index++]  = 'CODE';
        }

        $sql = sprintf(
            'INSERT INTO canada_post_service (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'VISIBLE':
                        $stmt->bindValue($identifier, $this->visible, PDO::PARAM_INT);
                        break;
                    case 'CODE':
                        $stmt->bindValue($identifier, $this->code, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CanadaPostServiceTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getVisible();
                break;
            case 2:
                return $this->getCode();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['CanadaPostService'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['CanadaPostService'][$this->getPrimaryKey()] = true;
        $keys = CanadaPostServiceTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getVisible(),
            $keys[2] => $this->getCode(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collCanadaPostOrders) {
                $result['CanadaPostOrders'] = $this->collCanadaPostOrders->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCanadaPostServiceI18ns) {
                $result['CanadaPostServiceI18ns'] = $this->collCanadaPostServiceI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = CanadaPostServiceTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setVisible($value);
                break;
            case 2:
                $this->setCode($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = CanadaPostServiceTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setVisible($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCode($arr[$keys[2]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CanadaPostServiceTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CanadaPostServiceTableMap::ID)) $criteria->add(CanadaPostServiceTableMap::ID, $this->id);
        if ($this->isColumnModified(CanadaPostServiceTableMap::VISIBLE)) $criteria->add(CanadaPostServiceTableMap::VISIBLE, $this->visible);
        if ($this->isColumnModified(CanadaPostServiceTableMap::CODE)) $criteria->add(CanadaPostServiceTableMap::CODE, $this->code);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(CanadaPostServiceTableMap::DATABASE_NAME);
        $criteria->add(CanadaPostServiceTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \CanadaPost\Model\CanadaPostService (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setVisible($this->getVisible());
        $copyObj->setCode($this->getCode());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCanadaPostOrders() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCanadaPostOrder($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCanadaPostServiceI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCanadaPostServiceI18n($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \CanadaPost\Model\CanadaPostService Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('CanadaPostOrder' == $relationName) {
            return $this->initCanadaPostOrders();
        }
        if ('CanadaPostServiceI18n' == $relationName) {
            return $this->initCanadaPostServiceI18ns();
        }
    }

    /**
     * Clears out the collCanadaPostOrders collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCanadaPostOrders()
     */
    public function clearCanadaPostOrders()
    {
        $this->collCanadaPostOrders = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCanadaPostOrders collection loaded partially.
     */
    public function resetPartialCanadaPostOrders($v = true)
    {
        $this->collCanadaPostOrdersPartial = $v;
    }

    /**
     * Initializes the collCanadaPostOrders collection.
     *
     * By default this just sets the collCanadaPostOrders collection to an empty array (like clearcollCanadaPostOrders());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCanadaPostOrders($overrideExisting = true)
    {
        if (null !== $this->collCanadaPostOrders && !$overrideExisting) {
            return;
        }
        $this->collCanadaPostOrders = new ObjectCollection();
        $this->collCanadaPostOrders->setModel('\CanadaPost\Model\CanadaPostOrder');
    }

    /**
     * Gets an array of ChildCanadaPostOrder objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCanadaPostService is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCanadaPostOrder[] List of ChildCanadaPostOrder objects
     * @throws PropelException
     */
    public function getCanadaPostOrders($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCanadaPostOrdersPartial && !$this->isNew();
        if (null === $this->collCanadaPostOrders || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCanadaPostOrders) {
                // return empty collection
                $this->initCanadaPostOrders();
            } else {
                $collCanadaPostOrders = ChildCanadaPostOrderQuery::create(null, $criteria)
                    ->filterByCanadaPostService($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCanadaPostOrdersPartial && count($collCanadaPostOrders)) {
                        $this->initCanadaPostOrders(false);

                        foreach ($collCanadaPostOrders as $obj) {
                            if (false == $this->collCanadaPostOrders->contains($obj)) {
                                $this->collCanadaPostOrders->append($obj);
                            }
                        }

                        $this->collCanadaPostOrdersPartial = true;
                    }

                    reset($collCanadaPostOrders);

                    return $collCanadaPostOrders;
                }

                if ($partial && $this->collCanadaPostOrders) {
                    foreach ($this->collCanadaPostOrders as $obj) {
                        if ($obj->isNew()) {
                            $collCanadaPostOrders[] = $obj;
                        }
                    }
                }

                $this->collCanadaPostOrders = $collCanadaPostOrders;
                $this->collCanadaPostOrdersPartial = false;
            }
        }

        return $this->collCanadaPostOrders;
    }

    /**
     * Sets a collection of CanadaPostOrder objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $canadaPostOrders A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCanadaPostService The current object (for fluent API support)
     */
    public function setCanadaPostOrders(Collection $canadaPostOrders, ConnectionInterface $con = null)
    {
        $canadaPostOrdersToDelete = $this->getCanadaPostOrders(new Criteria(), $con)->diff($canadaPostOrders);


        $this->canadaPostOrdersScheduledForDeletion = $canadaPostOrdersToDelete;

        foreach ($canadaPostOrdersToDelete as $canadaPostOrderRemoved) {
            $canadaPostOrderRemoved->setCanadaPostService(null);
        }

        $this->collCanadaPostOrders = null;
        foreach ($canadaPostOrders as $canadaPostOrder) {
            $this->addCanadaPostOrder($canadaPostOrder);
        }

        $this->collCanadaPostOrders = $canadaPostOrders;
        $this->collCanadaPostOrdersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CanadaPostOrder objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CanadaPostOrder objects.
     * @throws PropelException
     */
    public function countCanadaPostOrders(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCanadaPostOrdersPartial && !$this->isNew();
        if (null === $this->collCanadaPostOrders || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCanadaPostOrders) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCanadaPostOrders());
            }

            $query = ChildCanadaPostOrderQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCanadaPostService($this)
                ->count($con);
        }

        return count($this->collCanadaPostOrders);
    }

    /**
     * Method called to associate a ChildCanadaPostOrder object to this object
     * through the ChildCanadaPostOrder foreign key attribute.
     *
     * @param    ChildCanadaPostOrder $l ChildCanadaPostOrder
     * @return   \CanadaPost\Model\CanadaPostService The current object (for fluent API support)
     */
    public function addCanadaPostOrder(ChildCanadaPostOrder $l)
    {
        if ($this->collCanadaPostOrders === null) {
            $this->initCanadaPostOrders();
            $this->collCanadaPostOrdersPartial = true;
        }

        if (!in_array($l, $this->collCanadaPostOrders->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCanadaPostOrder($l);
        }

        return $this;
    }

    /**
     * @param CanadaPostOrder $canadaPostOrder The canadaPostOrder object to add.
     */
    protected function doAddCanadaPostOrder($canadaPostOrder)
    {
        $this->collCanadaPostOrders[]= $canadaPostOrder;
        $canadaPostOrder->setCanadaPostService($this);
    }

    /**
     * @param  CanadaPostOrder $canadaPostOrder The canadaPostOrder object to remove.
     * @return ChildCanadaPostService The current object (for fluent API support)
     */
    public function removeCanadaPostOrder($canadaPostOrder)
    {
        if ($this->getCanadaPostOrders()->contains($canadaPostOrder)) {
            $this->collCanadaPostOrders->remove($this->collCanadaPostOrders->search($canadaPostOrder));
            if (null === $this->canadaPostOrdersScheduledForDeletion) {
                $this->canadaPostOrdersScheduledForDeletion = clone $this->collCanadaPostOrders;
                $this->canadaPostOrdersScheduledForDeletion->clear();
            }
            $this->canadaPostOrdersScheduledForDeletion[]= $canadaPostOrder;
            $canadaPostOrder->setCanadaPostService(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CanadaPostService is new, it will return
     * an empty collection; or if this CanadaPostService has previously
     * been saved, it will retrieve related CanadaPostOrders from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CanadaPostService.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCanadaPostOrder[] List of ChildCanadaPostOrder objects
     */
    public function getCanadaPostOrdersJoinAddress($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCanadaPostOrderQuery::create(null, $criteria);
        $query->joinWith('Address', $joinBehavior);

        return $this->getCanadaPostOrders($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this CanadaPostService is new, it will return
     * an empty collection; or if this CanadaPostService has previously
     * been saved, it will retrieve related CanadaPostOrders from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in CanadaPostService.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCanadaPostOrder[] List of ChildCanadaPostOrder objects
     */
    public function getCanadaPostOrdersJoinOrderAddress($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCanadaPostOrderQuery::create(null, $criteria);
        $query->joinWith('OrderAddress', $joinBehavior);

        return $this->getCanadaPostOrders($query, $con);
    }

    /**
     * Clears out the collCanadaPostServiceI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCanadaPostServiceI18ns()
     */
    public function clearCanadaPostServiceI18ns()
    {
        $this->collCanadaPostServiceI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCanadaPostServiceI18ns collection loaded partially.
     */
    public function resetPartialCanadaPostServiceI18ns($v = true)
    {
        $this->collCanadaPostServiceI18nsPartial = $v;
    }

    /**
     * Initializes the collCanadaPostServiceI18ns collection.
     *
     * By default this just sets the collCanadaPostServiceI18ns collection to an empty array (like clearcollCanadaPostServiceI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCanadaPostServiceI18ns($overrideExisting = true)
    {
        if (null !== $this->collCanadaPostServiceI18ns && !$overrideExisting) {
            return;
        }
        $this->collCanadaPostServiceI18ns = new ObjectCollection();
        $this->collCanadaPostServiceI18ns->setModel('\CanadaPost\Model\CanadaPostServiceI18n');
    }

    /**
     * Gets an array of ChildCanadaPostServiceI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCanadaPostService is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCanadaPostServiceI18n[] List of ChildCanadaPostServiceI18n objects
     * @throws PropelException
     */
    public function getCanadaPostServiceI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCanadaPostServiceI18nsPartial && !$this->isNew();
        if (null === $this->collCanadaPostServiceI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCanadaPostServiceI18ns) {
                // return empty collection
                $this->initCanadaPostServiceI18ns();
            } else {
                $collCanadaPostServiceI18ns = ChildCanadaPostServiceI18nQuery::create(null, $criteria)
                    ->filterByCanadaPostService($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCanadaPostServiceI18nsPartial && count($collCanadaPostServiceI18ns)) {
                        $this->initCanadaPostServiceI18ns(false);

                        foreach ($collCanadaPostServiceI18ns as $obj) {
                            if (false == $this->collCanadaPostServiceI18ns->contains($obj)) {
                                $this->collCanadaPostServiceI18ns->append($obj);
                            }
                        }

                        $this->collCanadaPostServiceI18nsPartial = true;
                    }

                    reset($collCanadaPostServiceI18ns);

                    return $collCanadaPostServiceI18ns;
                }

                if ($partial && $this->collCanadaPostServiceI18ns) {
                    foreach ($this->collCanadaPostServiceI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collCanadaPostServiceI18ns[] = $obj;
                        }
                    }
                }

                $this->collCanadaPostServiceI18ns = $collCanadaPostServiceI18ns;
                $this->collCanadaPostServiceI18nsPartial = false;
            }
        }

        return $this->collCanadaPostServiceI18ns;
    }

    /**
     * Sets a collection of CanadaPostServiceI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $canadaPostServiceI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCanadaPostService The current object (for fluent API support)
     */
    public function setCanadaPostServiceI18ns(Collection $canadaPostServiceI18ns, ConnectionInterface $con = null)
    {
        $canadaPostServiceI18nsToDelete = $this->getCanadaPostServiceI18ns(new Criteria(), $con)->diff($canadaPostServiceI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->canadaPostServiceI18nsScheduledForDeletion = clone $canadaPostServiceI18nsToDelete;

        foreach ($canadaPostServiceI18nsToDelete as $canadaPostServiceI18nRemoved) {
            $canadaPostServiceI18nRemoved->setCanadaPostService(null);
        }

        $this->collCanadaPostServiceI18ns = null;
        foreach ($canadaPostServiceI18ns as $canadaPostServiceI18n) {
            $this->addCanadaPostServiceI18n($canadaPostServiceI18n);
        }

        $this->collCanadaPostServiceI18ns = $canadaPostServiceI18ns;
        $this->collCanadaPostServiceI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CanadaPostServiceI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CanadaPostServiceI18n objects.
     * @throws PropelException
     */
    public function countCanadaPostServiceI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCanadaPostServiceI18nsPartial && !$this->isNew();
        if (null === $this->collCanadaPostServiceI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCanadaPostServiceI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCanadaPostServiceI18ns());
            }

            $query = ChildCanadaPostServiceI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCanadaPostService($this)
                ->count($con);
        }

        return count($this->collCanadaPostServiceI18ns);
    }

    /**
     * Method called to associate a ChildCanadaPostServiceI18n object to this object
     * through the ChildCanadaPostServiceI18n foreign key attribute.
     *
     * @param    ChildCanadaPostServiceI18n $l ChildCanadaPostServiceI18n
     * @return   \CanadaPost\Model\CanadaPostService The current object (for fluent API support)
     */
    public function addCanadaPostServiceI18n(ChildCanadaPostServiceI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collCanadaPostServiceI18ns === null) {
            $this->initCanadaPostServiceI18ns();
            $this->collCanadaPostServiceI18nsPartial = true;
        }

        if (!in_array($l, $this->collCanadaPostServiceI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCanadaPostServiceI18n($l);
        }

        return $this;
    }

    /**
     * @param CanadaPostServiceI18n $canadaPostServiceI18n The canadaPostServiceI18n object to add.
     */
    protected function doAddCanadaPostServiceI18n($canadaPostServiceI18n)
    {
        $this->collCanadaPostServiceI18ns[]= $canadaPostServiceI18n;
        $canadaPostServiceI18n->setCanadaPostService($this);
    }

    /**
     * @param  CanadaPostServiceI18n $canadaPostServiceI18n The canadaPostServiceI18n object to remove.
     * @return ChildCanadaPostService The current object (for fluent API support)
     */
    public function removeCanadaPostServiceI18n($canadaPostServiceI18n)
    {
        if ($this->getCanadaPostServiceI18ns()->contains($canadaPostServiceI18n)) {
            $this->collCanadaPostServiceI18ns->remove($this->collCanadaPostServiceI18ns->search($canadaPostServiceI18n));
            if (null === $this->canadaPostServiceI18nsScheduledForDeletion) {
                $this->canadaPostServiceI18nsScheduledForDeletion = clone $this->collCanadaPostServiceI18ns;
                $this->canadaPostServiceI18nsScheduledForDeletion->clear();
            }
            $this->canadaPostServiceI18nsScheduledForDeletion[]= clone $canadaPostServiceI18n;
            $canadaPostServiceI18n->setCanadaPostService(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->visible = null;
        $this->code = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collCanadaPostOrders) {
                foreach ($this->collCanadaPostOrders as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCanadaPostServiceI18ns) {
                foreach ($this->collCanadaPostServiceI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collCanadaPostOrders = null;
        $this->collCanadaPostServiceI18ns = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CanadaPostServiceTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildCanadaPostService The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildCanadaPostServiceI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collCanadaPostServiceI18ns) {
                foreach ($this->collCanadaPostServiceI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildCanadaPostServiceI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildCanadaPostServiceI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addCanadaPostServiceI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildCanadaPostService The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildCanadaPostServiceI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collCanadaPostServiceI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collCanadaPostServiceI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildCanadaPostServiceI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [title] column value.
         *
         * @return   string
         */
        public function getTitle()
        {
        return $this->getCurrentTranslation()->getTitle();
    }


        /**
         * Set the value of [title] column.
         *
         * @param      string $v new value
         * @return   \CanadaPost\Model\CanadaPostServiceI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

        return $this;
    }


        /**
         * Get the [chapo] column value.
         *
         * @return   string
         */
        public function getChapo()
        {
        return $this->getCurrentTranslation()->getChapo();
    }


        /**
         * Set the value of [chapo] column.
         *
         * @param      string $v new value
         * @return   \CanadaPost\Model\CanadaPostServiceI18n The current object (for fluent API support)
         */
        public function setChapo($v)
        {    $this->getCurrentTranslation()->setChapo($v);

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
