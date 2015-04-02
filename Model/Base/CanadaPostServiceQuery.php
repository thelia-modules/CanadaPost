<?php

namespace CanadaPost\Model\Base;

use \Exception;
use \PDO;
use CanadaPost\Model\CanadaPostService as ChildCanadaPostService;
use CanadaPost\Model\CanadaPostServiceI18nQuery as ChildCanadaPostServiceI18nQuery;
use CanadaPost\Model\CanadaPostServiceQuery as ChildCanadaPostServiceQuery;
use CanadaPost\Model\Map\CanadaPostServiceTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'canada_post_service' table.
 *
 *
 *
 * @method     ChildCanadaPostServiceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCanadaPostServiceQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildCanadaPostServiceQuery orderByCode($order = Criteria::ASC) Order by the code column
 *
 * @method     ChildCanadaPostServiceQuery groupById() Group by the id column
 * @method     ChildCanadaPostServiceQuery groupByVisible() Group by the visible column
 * @method     ChildCanadaPostServiceQuery groupByCode() Group by the code column
 *
 * @method     ChildCanadaPostServiceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCanadaPostServiceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCanadaPostServiceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCanadaPostServiceQuery leftJoinCanadaPostOrder($relationAlias = null) Adds a LEFT JOIN clause to the query using the CanadaPostOrder relation
 * @method     ChildCanadaPostServiceQuery rightJoinCanadaPostOrder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CanadaPostOrder relation
 * @method     ChildCanadaPostServiceQuery innerJoinCanadaPostOrder($relationAlias = null) Adds a INNER JOIN clause to the query using the CanadaPostOrder relation
 *
 * @method     ChildCanadaPostServiceQuery leftJoinCanadaPostServiceI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the CanadaPostServiceI18n relation
 * @method     ChildCanadaPostServiceQuery rightJoinCanadaPostServiceI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CanadaPostServiceI18n relation
 * @method     ChildCanadaPostServiceQuery innerJoinCanadaPostServiceI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the CanadaPostServiceI18n relation
 *
 * @method     ChildCanadaPostService findOne(ConnectionInterface $con = null) Return the first ChildCanadaPostService matching the query
 * @method     ChildCanadaPostService findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCanadaPostService matching the query, or a new ChildCanadaPostService object populated from the query conditions when no match is found
 *
 * @method     ChildCanadaPostService findOneById(int $id) Return the first ChildCanadaPostService filtered by the id column
 * @method     ChildCanadaPostService findOneByVisible(int $visible) Return the first ChildCanadaPostService filtered by the visible column
 * @method     ChildCanadaPostService findOneByCode(string $code) Return the first ChildCanadaPostService filtered by the code column
 *
 * @method     array findById(int $id) Return ChildCanadaPostService objects filtered by the id column
 * @method     array findByVisible(int $visible) Return ChildCanadaPostService objects filtered by the visible column
 * @method     array findByCode(string $code) Return ChildCanadaPostService objects filtered by the code column
 *
 */
abstract class CanadaPostServiceQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CanadaPost\Model\Base\CanadaPostServiceQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CanadaPost\\Model\\CanadaPostService', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCanadaPostServiceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCanadaPostServiceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CanadaPost\Model\CanadaPostServiceQuery) {
            return $criteria;
        }
        $query = new \CanadaPost\Model\CanadaPostServiceQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildCanadaPostService|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CanadaPostServiceTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CanadaPostServiceTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildCanadaPostService A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, VISIBLE, CODE FROM canada_post_service WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildCanadaPostService();
            $obj->hydrate($row);
            CanadaPostServiceTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildCanadaPostService|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CanadaPostServiceTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CanadaPostServiceTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CanadaPostServiceTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CanadaPostServiceTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CanadaPostServiceTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the visible column
     *
     * Example usage:
     * <code>
     * $query->filterByVisible(1234); // WHERE visible = 1234
     * $query->filterByVisible(array(12, 34)); // WHERE visible IN (12, 34)
     * $query->filterByVisible(array('min' => 12)); // WHERE visible > 12
     * </code>
     *
     * @param     mixed $visible The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(CanadaPostServiceTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(CanadaPostServiceTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CanadaPostServiceTableMap::VISIBLE, $visible, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CanadaPostServiceTableMap::CODE, $code, $comparison);
    }

    /**
     * Filter the query by a related \CanadaPost\Model\CanadaPostOrder object
     *
     * @param \CanadaPost\Model\CanadaPostOrder|ObjectCollection $canadaPostOrder  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function filterByCanadaPostOrder($canadaPostOrder, $comparison = null)
    {
        if ($canadaPostOrder instanceof \CanadaPost\Model\CanadaPostOrder) {
            return $this
                ->addUsingAlias(CanadaPostServiceTableMap::ID, $canadaPostOrder->getServiceId(), $comparison);
        } elseif ($canadaPostOrder instanceof ObjectCollection) {
            return $this
                ->useCanadaPostOrderQuery()
                ->filterByPrimaryKeys($canadaPostOrder->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCanadaPostOrder() only accepts arguments of type \CanadaPost\Model\CanadaPostOrder or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CanadaPostOrder relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function joinCanadaPostOrder($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CanadaPostOrder');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CanadaPostOrder');
        }

        return $this;
    }

    /**
     * Use the CanadaPostOrder relation CanadaPostOrder object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CanadaPost\Model\CanadaPostOrderQuery A secondary query class using the current class as primary query
     */
    public function useCanadaPostOrderQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCanadaPostOrder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CanadaPostOrder', '\CanadaPost\Model\CanadaPostOrderQuery');
    }

    /**
     * Filter the query by a related \CanadaPost\Model\CanadaPostServiceI18n object
     *
     * @param \CanadaPost\Model\CanadaPostServiceI18n|ObjectCollection $canadaPostServiceI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function filterByCanadaPostServiceI18n($canadaPostServiceI18n, $comparison = null)
    {
        if ($canadaPostServiceI18n instanceof \CanadaPost\Model\CanadaPostServiceI18n) {
            return $this
                ->addUsingAlias(CanadaPostServiceTableMap::ID, $canadaPostServiceI18n->getId(), $comparison);
        } elseif ($canadaPostServiceI18n instanceof ObjectCollection) {
            return $this
                ->useCanadaPostServiceI18nQuery()
                ->filterByPrimaryKeys($canadaPostServiceI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCanadaPostServiceI18n() only accepts arguments of type \CanadaPost\Model\CanadaPostServiceI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CanadaPostServiceI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function joinCanadaPostServiceI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CanadaPostServiceI18n');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'CanadaPostServiceI18n');
        }

        return $this;
    }

    /**
     * Use the CanadaPostServiceI18n relation CanadaPostServiceI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CanadaPost\Model\CanadaPostServiceI18nQuery A secondary query class using the current class as primary query
     */
    public function useCanadaPostServiceI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCanadaPostServiceI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CanadaPostServiceI18n', '\CanadaPost\Model\CanadaPostServiceI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCanadaPostService $canadaPostService Object to remove from the list of results
     *
     * @return ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function prune($canadaPostService = null)
    {
        if ($canadaPostService) {
            $this->addUsingAlias(CanadaPostServiceTableMap::ID, $canadaPostService->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the canada_post_service table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CanadaPostServiceTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            CanadaPostServiceTableMap::clearInstancePool();
            CanadaPostServiceTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCanadaPostService or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCanadaPostService object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CanadaPostServiceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CanadaPostServiceTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CanadaPostServiceTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CanadaPostServiceTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'CanadaPostServiceI18n';

        return $this
            ->joinCanadaPostServiceI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCanadaPostServiceQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('CanadaPostServiceI18n');
        $this->with['CanadaPostServiceI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCanadaPostServiceI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CanadaPostServiceI18n', '\CanadaPost\Model\CanadaPostServiceI18nQuery');
    }

} // CanadaPostServiceQuery
