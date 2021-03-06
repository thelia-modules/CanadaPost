<?php
/**
* This class has been generated by TheliaStudio
* For more information, see https://github.com/thelia-modules/TheliaStudio
*/

namespace CanadaPost\Loop\Base;

use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;
use CanadaPost\Model\CanadaPostServiceQuery;

/**
 * Class CanadaPostService
 * @package CanadaPost\Loop\Base
 * @author TheliaStudio
 */
class CanadaPostService extends BaseI18nLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \CanadaPost\Model\CanadaPostService $entry */
        foreach ($loopResult->getResultDataCollection() as $entry) {
            $row = new LoopResultRow($entry);

            $row
                ->set("ID", $entry->getId())
                ->set("VISIBLE", $entry->getVisible())
                ->set("CODE", $entry->getCode())
                ->set("TITLE", $entry->getVirtualColumn("i18n_TITLE"))
                ->set("CHAPO", $entry->getVirtualColumn("i18n_CHAPO"))
            ;

            $this->addMoreResults($row, $entry);

            $loopResult->addRow($row);
        }

        return $loopResult;
    }

    /**
     * Definition of loop arguments
     *
     * example :
     *
     * public function getArgDefinitions()
     * {
     *  return new ArgumentCollection(
     *
     *       Argument::createIntListTypeArgument('id'),
     *           new Argument(
     *           'ref',
     *           new TypeCollection(
     *               new Type\AlphaNumStringListType()
     *           )
     *       ),
     *       Argument::createIntListTypeArgument('category'),
     *       Argument::createBooleanTypeArgument('new'),
     *       ...
     *   );
     * }
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument("id"),
            Argument::createBooleanOrBothTypeArgument("visible", 1),
            Argument::createAnyTypeArgument("code"),
            Argument::createAnyTypeArgument("title"),
            Argument::createEnumListTypeArgument(
                "order",
                [
                    "id",
                    "id-reverse",
                    "visible",
                    "visible-reverse",
                    "code",
                    "code-reverse",
                    "title",
                    "title-reverse",
                    "chapo",
                    "chapo-reverse",
                ],
                "id"
            )
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = new CanadaPostServiceQuery();
        $this->configureI18nProcessing($query, ["TITLE", "CHAPO", ]);

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (BooleanOrBothType::ANY !== $visible = $this->getVisible()) {
            $query->filterByVisible($visible);
        }

        if (null !== $code = $this->getCode()) {
            $code = array_map("trim", explode(",", $code));
            $query->filterByCode($code);
        }

        if (null !== $title = $this->getTitle()) {
            $title = array_map("trim", explode(",", $title));
            $query->filterByTitle($title);
        }

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case "id":
                    $query->orderById();
                    break;
                case "id-reverse":
                    $query->orderById(Criteria::DESC);
                    break;
                case "visible":
                    $query->orderByVisible();
                    break;
                case "visible-reverse":
                    $query->orderByVisible(Criteria::DESC);
                    break;
                case "code":
                    $query->orderByCode();
                    break;
                case "code-reverse":
                    $query->orderByCode(Criteria::DESC);
                    break;
                case "title":
                    $query->addAscendingOrderByColumn("i18n_TITLE");
                    break;
                case "title-reverse":
                    $query->addDescendingOrderByColumn("i18n_TITLE");
                    break;
                case "chapo":
                    $query->addAscendingOrderByColumn("i18n_CHAPO");
                    break;
                case "chapo-reverse":
                    $query->addDescendingOrderByColumn("i18n_CHAPO");
                    break;
            }
        }

        return $query;
    }

    protected function addMoreResults(LoopResultRow $row, $entryObject)
    {
    }
}
