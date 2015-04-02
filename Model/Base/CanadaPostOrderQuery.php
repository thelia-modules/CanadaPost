<?php

namespace CanadaPost\Model\Base;

use \Exception;
use \PDO;
use CanadaPost\Model\CanadaPostOrder as ChildCanadaPostOrder;
use CanadaPost\Model\CanadaPostOrderQuery as ChildCanadaPostOrderQuery;
use CanadaPost\Model\Map\CanadaPostOrderTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Address;
use Thelia\Model\OrderAddress;

/**
 * Base class that represents a query for the 'canada_post_order' table.
 *
 *
 *
 * @method     ChildCanadaPostOrderQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCanadaPostOrderQuery orderByAddressId($order = Criteria::ASC) Order by the address_id column
 * @method     ChildCanadaPostOrderQuery orderByOrderAddressId($order = Criteria::ASC) Order by the order_address_id column
 * @method     ChildCanadaPostOrderQuery orderByServiceId($order = Criteria::ASC) Order by the service_id column
 * @method     ChildCanadaPostOrderQuery orderByOptions($order = Criteria::ASC) Order by the options column
 *
 * @method     ChildCanadaPostOrderQuery groupById() Group by the id column
 * @method     ChildCanadaPostOrderQuery groupByAddressId() Group by the address_id column
 * @method     ChildCanadaPostOrderQuery groupByOrderAddressId() Group by the order_address_id column
 * @method     ChildCanadaPostOrderQuery groupByServiceId() Group by the service_id column
 * @method     ChildCanadaPostOrderQuery groupByOptions() Group by the options column
 *
 * @method     ChildCanadaPostOrderQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCanadaPostOrderQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCanadaPostOrderQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCanadaPostOrderQuery leftJoinCanadaPostService($relationAlias = null) Adds a LEFT JOIN clause to the query using the CanadaPostService relation
 * @method     ChildCanadaPostOrderQuery rightJoinCanadaPostService($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CanadaPostService relation
 * @method     ChildCanadaPostOrderQuery innerJoinCanadaPostService($relationAlias = null) Adds a INNER JOIN clause to the query using the CanadaPostService relation
 *
 * @method     ChildCanadaPostOrderQuery leftJoinAddress($relationAlias = null) Adds a LEFT JOIN clause to the query using the Address relation
 * @method     ChildCanadaPostOrderQuery rightJoinAddress($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Address relation
 * @method     ChildCanadaPostOrderQuery innerJoinAddress($relationAlias = null) Adds a INNER JOIN clause to the query using the Address relation
 *
 * @method     ChildCanadaPostOrderQuery leftJoinOrderAddress($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderAddress relation
 * @method     ChildCanadaPostOrderQuery rightJoinOrderAddress($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderAddress relation
 * @method     ChildCanadaPostOrderQuery innerJoinOrderAddress($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderAddress relation
 *
 * @method     ChildCanadaPostOrder findOne(ConnectionInterface $con = null) Return the first ChildCanadaPostOrder matching the query
 * @method     ChildCanadaPostOrder findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCanadaPostOrder matching the query, or a new ChildCanadaPostOrder object populated from the query conditions when no match is found
 *
 * @method     ChildCanadaPostOrder findOneById(int $id) Return the first ChildCanadaPostOrder filtered by the id column
 * @method     ChildCanadaPostOrder findOneByAddressId(int $address_id) Return the first ChildCanadaPostOrder filtered by the address_id column
 * @method     ChildCanadaPostOrder findOneByOrderAddressId(int $order_address_id) Return the first ChildCanadaPostOrder filtered by the order_address_id column
 * @method     ChildCanadaPostOrder findOneByServiceId(int $service_id) Return the first ChildCanadaPostOrder filtered by the service_id column
 * @method     ChildCanadaPostOrder findOneByOptions(string $options) Return the first ChildCanadaPostOrder filtered by the options column
 *
 * @method     array findById(int $id) Return ChildCanadaPostOrder objects filtered by the id column
 * @method     array findByAddressId(int $address_id) Return ChildCanadaPostOrder objects filtered by the address_id column
 * @method     array findByOrderAddressId(int $order_address_id) Return ChildCanadaPostOrder objects filtered by the order_address_id column
 * @method     array findByServiceId(int $service_id) Return ChildCanadaPostOrder objects filtered by the service_id column
 * @method     array findByOptions(string $options) Return ChildCanadaPostOrder objects filtered by the options column
 *
 */
abstract class CanadaPostOrderQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \CanadaPost\Model\Base\CanadaPostOrderQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\CanadaPost\\Model\\CanadaPostOrder', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCanadaPostOrderQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCanadaPostOrderQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \CanadaPost\Model\CanadaPostOrderQuery) {
            return $criteria;
        }
        $query = new \CanadaPost\Model\CanadaPostOrderQuery();
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
     * @return ChildCanadaPostOrder|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CanadaPostOrderTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CanadaPostOrderTableMap::DATABASE_NAME);
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
     * @return   ChildCanadaPostOrder A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ADDRESS_ID, ORDER_ADDRESS_ID, SERVICE_ID, OPTIONS FROM canada_post_order WHERE ID = :p0';
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
            $obj = new ChildCanadaPostOrder();
            $obj->hydrate($row);
            CanadaPostOrderTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCanadaPostOrder|array|mixed the result, formatted by the current formatter
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
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CanadaPostOrderTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CanadaPostOrderTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CanadaPostOrderTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the address_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAddressId(1234); // WHERE address_id = 1234
     * $query->filterByAddressId(array(12, 34)); // WHERE address_id IN (12, 34)
     * $query->filterByAddressId(array('min' => 12)); // WHERE address_id > 12
     * </code>
     *
     * @see       filterByAddress()
     *
     * @param     mixed $addressId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByAddressId($addressId = null, $comparison = null)
    {
        if (is_array($addressId)) {
            $useMinMax = false;
            if (isset($addressId['min'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::ADDRESS_ID, $addressId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($addressId['max'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::ADDRESS_ID, $addressId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CanadaPostOrderTableMap::ADDRESS_ID, $addressId, $comparison);
    }

    /**
     * Filter the query on the order_address_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderAddressId(1234); // WHERE order_address_id = 1234
     * $query->filterByOrderAddressId(array(12, 34)); // WHERE order_address_id IN (12, 34)
     * $query->filterByOrderAddressId(array('min' => 12)); // WHERE order_address_id > 12
     * </code>
     *
     * @see       filterByOrderAddress()
     *
     * @param     mixed $orderAddressId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByOrderAddressId($orderAddressId = null, $comparison = null)
    {
        if (is_array($orderAddressId)) {
            $useMinMax = false;
            if (isset($orderAddressId['min'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::ORDER_ADDRESS_ID, $orderAddressId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderAddressId['max'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::ORDER_ADDRESS_ID, $orderAddressId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CanadaPostOrderTableMap::ORDER_ADDRESS_ID, $orderAddressId, $comparison);
    }

    /**
     * Filter the query on the service_id column
     *
     * Example usage:
     * <code>
     * $query->filterByServiceId(1234); // WHERE service_id = 1234
     * $query->filterByServiceId(array(12, 34)); // WHERE service_id IN (12, 34)
     * $query->filterByServiceId(array('min' => 12)); // WHERE service_id > 12
     * </code>
     *
     * @see       filterByCanadaPostService()
     *
     * @param     mixed $serviceId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByServiceId($serviceId = null, $comparison = null)
    {
        if (is_array($serviceId)) {
            $useMinMax = false;
            if (isset($serviceId['min'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::SERVICE_ID, $serviceId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($serviceId['max'])) {
                $this->addUsingAlias(CanadaPostOrderTableMap::SERVICE_ID, $serviceId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CanadaPostOrderTableMap::SERVICE_ID, $serviceId, $comparison);
    }

    /**
     * Filter the query on the options column
     *
     * Example usage:
     * <code>
     * $query->filterByOptions('fooValue');   // WHERE options = 'fooValue'
     * $query->filterByOptions('%fooValue%'); // WHERE options LIKE '%fooValue%'
     * </code>
     *
     * @param     string $options The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByOptions($options = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($options)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $options)) {
                $options = str_replace('*', '%', $options);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CanadaPostOrderTableMap::OPTIONS, $options, $comparison);
    }

    /**
     * Filter the query by a related \CanadaPost\Model\CanadaPostService object
     *
     * @param \CanadaPost\Model\CanadaPostService|ObjectCollection $canadaPostService The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByCanadaPostService($canadaPostService, $comparison = null)
    {
        if ($canadaPostService instanceof \CanadaPost\Model\CanadaPostService) {
            return $this
                ->addUsingAlias(CanadaPostOrderTableMap::SERVICE_ID, $canadaPostService->getId(), $comparison);
        } elseif ($canadaPostService instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CanadaPostOrderTableMap::SERVICE_ID, $canadaPostService->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCanadaPostService() only accepts arguments of type \CanadaPost\Model\CanadaPostService or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CanadaPostService relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function joinCanadaPostService($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CanadaPostService');

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
            $this->addJoinObject($join, 'CanadaPostService');
        }

        return $this;
    }

    /**
     * Use the CanadaPostService relation CanadaPostService object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \CanadaPost\Model\CanadaPostServiceQuery A secondary query class using the current class as primary query
     */
    public function useCanadaPostServiceQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCanadaPostService($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CanadaPostService', '\CanadaPost\Model\CanadaPostServiceQuery');
    }

    /**
     * Filter the query by a related \Thelia\Model\Address object
     *
     * @param \Thelia\Model\Address|ObjectCollection $address The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByAddress($address, $comparison = null)
    {
        if ($address instanceof \Thelia\Model\Address) {
            return $this
                ->addUsingAlias(CanadaPostOrderTableMap::ADDRESS_ID, $address->getId(), $comparison);
        } elseif ($address instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CanadaPostOrderTableMap::ADDRESS_ID, $address->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAddress() only accepts arguments of type \Thelia\Model\Address or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Address relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function joinAddress($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Address');

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
            $this->addJoinObject($join, 'Address');
        }

        return $this;
    }

    /**
     * Use the Address relation Address object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\AddressQuery A secondary query class using the current class as primary query
     */
    public function useAddressQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinAddress($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Address', '\Thelia\Model\AddressQuery');
    }

    /**
     * Filter the query by a related \Thelia\Model\OrderAddress object
     *
     * @param \Thelia\Model\OrderAddress|ObjectCollection $orderAddress The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function filterByOrderAddress($orderAddress, $comparison = null)
    {
        if ($orderAddress instanceof \Thelia\Model\OrderAddress) {
            return $this
                ->addUsingAlias(CanadaPostOrderTableMap::ORDER_ADDRESS_ID, $orderAddress->getId(), $comparison);
        } elseif ($orderAddress instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CanadaPostOrderTableMap::ORDER_ADDRESS_ID, $orderAddress->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrderAddress() only accepts arguments of type \Thelia\Model\OrderAddress or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderAddress relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function joinOrderAddress($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderAddress');

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
            $this->addJoinObject($join, 'OrderAddress');
        }

        return $this;
    }

    /**
     * Use the OrderAddress relation OrderAddress object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\OrderAddressQuery A secondary query class using the current class as primary query
     */
    public function useOrderAddressQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinOrderAddress($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderAddress', '\Thelia\Model\OrderAddressQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCanadaPostOrder $canadaPostOrder Object to remove from the list of results
     *
     * @return ChildCanadaPostOrderQuery The current query, for fluid interface
     */
    public function prune($canadaPostOrder = null)
    {
        if ($canadaPostOrder) {
            $this->addUsingAlias(CanadaPostOrderTableMap::ID, $canadaPostOrder->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the canada_post_order table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CanadaPostOrderTableMap::DATABASE_NAME);
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
            CanadaPostOrderTableMap::clearInstancePool();
            CanadaPostOrderTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCanadaPostOrder or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCanadaPostOrder object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CanadaPostOrderTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CanadaPostOrderTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        CanadaPostOrderTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            CanadaPostOrderTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CanadaPostOrderQuery
