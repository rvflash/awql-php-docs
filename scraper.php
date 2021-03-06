<?php
/**
 * Use to build Yaml files about tables or fields properties of Google Adwords reports
 */
ini_set("display_errors", "1");
ini_set("error_reporting", E_ALL);

require("vendor/php-html-parser/htmlparser.php");

/**
 * @param array $aData
 * @return int
 */
function getMaxKeyLength(array $aData)
{
    $iMaxKeyLength = 0;
    foreach (array_keys($aData) as $sData) {
        if ($iMaxKeyLength < ($iCurrentLength = strlen($sData))) {
            $iMaxKeyLength = $iCurrentLength;
        }
    }
    return $iMaxKeyLength;
}

/**
 * Used to sort array by adding on top the field with more uncompatibilities
 * @param array $aFieldsA
 * @param array $aFieldsB
 * @return bool
 */
function sortByUncompatibilities ($aFieldsA, $aFieldsB)
{
    $iNbFieldsA = count($aFieldsA);
    $iNbFieldsB = count($aFieldsB);

    if ($iNbFieldsA == $iNbFieldsB) {
        return 0;
    }
    return ($iNbFieldsA < $iNbFieldsB ? 1 : -1);
}

// All available reports in Google Adwords
$aUrl = array(
    "ACCOUNT_PERFORMANCE_REPORT" =>
        array(
            "key" => "AccountDescriptiveName",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/account-performance-report",
        ),
    "AD_CUSTOMIZERS_FEED_ITEM_REPORT" =>
        array(
            "key" => "FeedItemId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/ad-customizers-feed-item-report",
        ),
    "AD_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/ad-performance-report",
        ),
    "ADGROUP_PERFORMANCE_REPORT" =>
        array(
            "key" => "AdGroupId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/adgroup-performance-report",
        ),
    "AGE_RANGE_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/age-range-performance-report",
        ),
    "AUDIENCE_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/audience-performance-report",
        ),
    "AUTOMATIC_PLACEMENTS_PERFORMANCE_REPORT" =>
        array(
            "key" => "Domain",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/automatic-placements-performance-report",
        ),
    "BID_GOAL_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/bid-goal-performance-report",
        ),
    "BUDGET_PERFORMANCE_REPORT" =>
        array(
            "key" => "BudgetId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/budget-performance-report",
        ),
    "CALL_METRICS_CALL_DETAILS_REPORT" =>
        array(
            "key" => "AdGroupId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/call-metrics-call-details-report",
        ),
    "CAMPAIGN_AD_SCHEDULE_TARGET_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-ad-schedule-target-report",
        ),
    "CAMPAIGN_LOCATION_TARGET_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-location-target-report",
        ),
    "CAMPAIGN_NEGATIVE_KEYWORDS_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-negative-keywords-performance-report",
        ),
    "CAMPAIGN_NEGATIVE_LOCATIONS_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-negative-locations-report",
        ),
    "CAMPAIGN_NEGATIVE_PLACEMENTS_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-negative-placements-performance-report",
        ),
    "CAMPAIGN_PERFORMANCE_REPORT" =>
        array(
            "key" => "CampaignId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-performance-report",
        ),
    /*
    "CAMPAIGN_PLATFORM_TARGET_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-platform-target-report",
        ),
    */
    "CAMPAIGN_SHARED_SET_REPORT" =>
        array(
            "key" => "SharedSetName",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-shared-set-report",
        ),
    "CLICK_PERFORMANCE_REPORT" =>
        array(
            "key" => "GclId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/click-performance-report",
        ),
    "CREATIVE_CONVERSION_REPORT" =>
        array(
            "key" => "CreativeId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/creative-conversion-report",
        ),
    "CRITERIA_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/criteria-performance-report",
        ),
    /*
    "DESTINATION_URL_REPORT" =>
        array(
            "key" => "CriteriaDestinationUrl",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/destination-url-report",
        ),
    */
    "DISPLAY_KEYWORD_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/display-keyword-performance-report",
        ),
    "DISPLAY_TOPICS_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/display-topics-performance-report",
        ),
    "FINAL_URL_REPORT" =>
        array(
            "key" => "EffectiveFinalUrl",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/final-url-report",
        ),
    "GENDER_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/gender-performance-report",
        ),
    "GEO_PERFORMANCE_REPORT" =>
        array(
            "key" => "CityCriteriaId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/geo-performance-report",
        ),
    "KEYWORDLESS_CATEGORY_REPORT" =>
        array(
            "key" => "Category2",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/keywordless-category-report",
        ),
    "KEYWORDLESS_QUERY_REPORT" =>
        array(
            "key" => "Query",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/keywordless-query-report",
        ),
    "KEYWORDS_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/keywords-performance-report",
        ),
    "LABEL_REPORT" =>
        array(
            "key" => "LabelId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/label-report",
        ),
    "LANDING_PAGE_REPORT" =>
        array(
            "key" => "UnexpandedFinalUrlString",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/landing-page-report",
        ),
    "PAID_ORGANIC_QUERY_REPORT" =>
        array(
            "key" => "SearchQuery",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/paid-organic-query-report",
        ),
    "PARENTAL_STATUS_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/parental-status-performance-report",
        ),
    "PLACEHOLDER_FEED_ITEM_REPORT" =>
        array(
            "key" => "FeedItemId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/placeholder-feed-item-report",
        ),
    "PLACEHOLDER_REPORT" =>
        array(
            "key" => "ClickType",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/placeholder-report",
        ),
    "PLACEMENT_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/placement-performance-report",
        ),
    "PRODUCT_PARTITION_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/product-partition-report",
        ),
    "SEARCH_QUERY_PERFORMANCE_REPORT" =>
        array(
            "key" => "Query",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/search-query-performance-report",
        ),
    "SHARED_SET_CRITERIA_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/shared-set-criteria-report",
        ),
    "SHARED_SET_REPORT" =>
        array(
            "key" => "SharedSetId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/shared-set-report",
        ),
    "SHOPPING_PERFORMANCE_REPORT" =>
        array(
            "key" => "OfferId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/shopping-performance-report",
        ),
    "URL_PERFORMANCE_REPORT" =>
        array(
            "key" => "Url",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/url-performance-report",
        ),
    "USER_AD_DISTANCE_REPORT" =>
        array(
            "key" => "DistanceBucket",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/user-ad-distance-report",
        ),
    "TOP_CONTENT_PERFORMANCE_REPORT" =>
        array(
            "key" => "Id",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/top-content-performance-report",
        ),
    "VIDEO_PERFORMANCE_REPORT" =>
        array(
            "key" => "VideoId",
            "url" => "https://developers.google.com/adwords/api/docs/appendix/reports/video-performance-report"
        ),
);

const ADWORDS_API_VERSION = "v201809";

const YAML_DIRECTORY = "adwords";
const YAML_COMPATIBILITY_DIR = "compatibility"; // For each tables, list all fields with incompatibility with others (from the more inconsistent at least )
const YAML_TABLES = "tables.yaml"; // List all tables available, for each gives fields inside
const YAML_BLACKLIST_FIELDS = "blacklisted_fields.yaml"; // For each tables, list all fields to exclude
const YAML_FIELDS = "fields.yaml"; // List all fields available, for each gives type of the data
const YAML_KEYS = "keys.yaml"; // List all tables available, for each gives segmented fields
const YAML_EXTRA = "extra.yaml"; // List all fields available, for each gives more information about it
const YAML_REPORTS = "reports.yaml"; // Resume in one file, all properties of each report

const XPATH_FIELD = ".devsite-article-body tbody tr";
const XPATH_FIELD_NAME = "h3";
const XPATH_FIELD_INFO = ".nested td";
const XPATH_FIELD_EXTRA = "p";
const XPATH_FIELD_ENUM = ".expandable .nested code";
const XPATH_FIELD_COMPATIBILITY = ".expandable div code";

$aFields = array();
$aRateFields = array();
$aKeys = array();
$aExtra = array();
$aTables = array();
$aCompatibility = array();
$iTableNb = count($aUrl);
$iTablePos = 1;
$aReports = array();

# For each Url, fetch and parse Google Adwords page
foreach ($aUrl as $sTableName => $aTable)
{
    $aTables[$sTableName] = array();
    $aKeys[$sTableName] = array();
    $aCompatibility[$sTableName] = array();
    $aRateFields[$sTableName] = array();
    $aReport = array("name" => $sTableName, "cols" => array(), "aggr" => $aTable["key"]);

    echo "Fecth ".$iTablePos."/".$iTableNb.": ".$aTable["url"]."\n";

    $html = HtmlParser::from_file($aTable["url"]);
    foreach ($html->find(XPATH_FIELD) as $oField)
    {
        if (null !== ($oFieldName = $oField->find(XPATH_FIELD_NAME, 0))) {
            # Field name
            $sFieldName = trim($oFieldName->text);
            $aColumn = array("name" => $sFieldName);
            $oFieldInfos = $oField->find(XPATH_FIELD_INFO);
            foreach ($oFieldInfos as $iIndex => $oFieldInfo)
            {
                if ("Type" == $oFieldInfo->text) {
                    # Type of field
                    $aColumn["kind"] = trim($oFieldInfos[($iIndex + 1)]->text);
                    # Add enum values ?
                    if (null != ($oFieldEnums = $oField->find(XPATH_FIELD_ENUM))) {
                        $aColumn["enum"] = array();
                        foreach ($oFieldEnums as $oFieldEnum)
                        {
                            $aColumn["enum"][] = trim($oFieldEnum->text);
                        }
                    }
                    # Column already processed?
                    if (false == isset($aFields[$sFieldName])) {
                        $aFields[$sFieldName] = $aColumn["kind"];
                        # Add enum values ?
                        if (null != ($oFieldEnums = $oField->find(XPATH_FIELD_ENUM))) {
                            $aFields[$sFieldName] .= " (".implode(" ", $aColumn["enum"]).")";
                        }
                    }
                } elseif ("Behavior" == $oFieldInfo->text && "Segment" == $oFieldInfos[($iIndex + 1)]->text) {
                    # Field can be a structuring key
                    $aKeys[$sTableName][] = $sFieldName;
                    $aColumn["sgmt"] = true;
                } elseif ("Supports Zero Impressions" == $oFieldInfo->text) {
                    $aColumn["zero"] = true;
                }

                # This field has not compatible with others fields ?
                if (false == isset($aCompatibility[$sTableName][$sFieldName])) {
                    $aColumn["notc"] = array();
                    $oFieldCompatibilities = $oField->find(XPATH_FIELD_COMPATIBILITY);
                    foreach ($oFieldCompatibilities as $oFieldCompatibility)
                    {
                        $aColumn["notc"][] = trim($oFieldCompatibility->text);
                    }
                    $aCompatibility[$sTableName][$sFieldName] = $aColumn["notc"];
                }
            }
            if (false == isset($aExtra[$sFieldName])) {
                # Give a description for this field
                $aExtra[$sFieldName] = trim(strtok($oField->find(XPATH_FIELD_EXTRA, 0)->text, "\n"));
            }
            if (false !== stripos($aExtra[$sFieldName], 'percentage return')) {
                # Detect rate / percent fields
                $aRateFields[$sTableName][] = $sFieldName;
            }
            # Add this field in this table
            $aTables[$sTableName][] = $sFieldName;
            $aReport["cols"][] = $aColumn;
        }
    }
    $aReports[] = $aReport;
    $iTablePos++;
}

# Create thesaurus in Yaml format

# > Tables with all properties
if (false == empty($aReports)) {
    $sReportsFilePath = __DIR__."/".YAML_DIRECTORY."/".ADWORDS_API_VERSION."/".YAML_REPORTS;
    $aReportsToFile = array();
    $aReportsToFile[] = "reports:\n";
    foreach ($aReports as $sTableName => $aReport) {
        $aReportsToFile[] = "  - name: ".$aReport["name"]."\n";
        $aReportsToFile[] = "    aggr: ".$aReport["aggr"]."\n";
        $aReportsToFile[] = "    cols: "."\n";
        foreach ($aReport["cols"] as $aColumn) {
            foreach ($aColumn as $sKey => $mValue) {
                if ($sKey == "name") {
                    $aReportsToFile[] = "      - ".$sKey.": ".$mValue."\n";
                } elseif (is_array($mValue)) {
                    if (empty($mValue)) {
                        continue;
                    }
                    $aReportsToFile[] = "        ".$sKey.": [ ".implode(", ", $mValue)." ]\n";
                } elseif (is_bool($mValue)) {
                    $aReportsToFile[] = "        ".$sKey.": ".($mValue ? "true" : "false")."\n";
                } else {
                    $aReportsToFile[] = "        ".$sKey.": ".$mValue."\n";
                }
            }
        }
    }
    file_put_contents($sReportsFilePath, $aReportsToFile);
    echo "Build thesaurus for reports in path: ".$sReportsFilePath."\n";
}

# > Tables with fields
if (false == empty($aTables)) {
    $sTablesFilePath = __DIR__."/".YAML_DIRECTORY."/".ADWORDS_API_VERSION."/".YAML_TABLES;
    $iTableColumnSize = getMaxKeyLength($aTables);
    $aTablesToFile = array();
    foreach ($aTables as $sTableName => $aTableFields) {
        $aTablesToFile[] = str_pad($sTableName, ($iTableColumnSize + 2), " ").": ".implode(" ", $aTableFields)."\n";
    }
    file_put_contents($sTablesFilePath, $aTablesToFile);
    echo "Build thesaurus for tables in path: ".$sTablesFilePath."\n";
}

# > Fields with data type
if (false == empty($aFields)) {
    $sFieldsFilePath = __DIR__."/".YAML_DIRECTORY."/".ADWORDS_API_VERSION."/".YAML_FIELDS;
    $iColumnSize = getMaxKeyLength($aFields);
    $aFieldsToFile = array();
    foreach ($aFields as $sFieldName => $sFieldType) {
        $aFieldsToFile[] = str_pad($sFieldName, ($iColumnSize + 2), " ").": ".$sFieldType."\n";
    }
    file_put_contents($sFieldsFilePath, $aFieldsToFile);
    echo "Build thesaurus for fields in path: ".$sFieldsFilePath."\n";
}

# > Tables with keys
if (false == empty($aKeys)) {
    $sKeysFilePath = __DIR__."/".YAML_DIRECTORY."/".ADWORDS_API_VERSION."/".YAML_KEYS;
    $iColumnSize = getMaxKeyLength($aKeys);
    $aKeysToFile = array();
    foreach ($aKeys as $sTableName => $aTableFields) {
        $aKeysToFile[] = str_pad($sTableName, ($iColumnSize + 2), " ").": ".implode(" ", $aTableFields)."\n";
    }
    file_put_contents($sKeysFilePath, $aKeysToFile);
    echo "Build thesaurus for table's keys in path: ".$sKeysFilePath."\n";
}

# > Fields with description
if (false == empty($aExtra)) {
    $sExtraFilePath = __DIR__."/".YAML_DIRECTORY."/".ADWORDS_API_VERSION."/".YAML_EXTRA;
    $iColumnSize = getMaxKeyLength($aExtra);
    $aExtraToFile = array();
    foreach ($aExtra as $sFieldName => $sExtra) {
        $aExtraToFile[] = str_pad($sFieldName, ($iColumnSize + 2), " ").": ".$sExtra."\n";
    }
    file_put_contents($sExtraFilePath, $aExtraToFile);
    echo "Build thesaurus for field's description in path: ".$sExtraFilePath."\n";
}

# > Tables with incompatible fields
if (false == empty($aCompatibility)) {
    $sCompatibilitiesFilePath = __DIR__."/".YAML_DIRECTORY."/".ADWORDS_API_VERSION."/".YAML_COMPATIBILITY_DIR."/";
    $sBlacklistedFilePath = __DIR__."/".YAML_DIRECTORY."/".ADWORDS_API_VERSION."/".YAML_BLACKLIST_FIELDS;

    # Reset environment to compute blacklisted fields
    if (file_exists($sBlacklistedFilePath)) {
        unlink($sBlacklistedFilePath);
    }

    foreach ($aCompatibility as $sTableName => $aTableFields) {
        uasort($aTableFields, "sortByUncompatibilities");

        # List all uncompatibles fields in this tables
        $aCompatibilitiesToFile = array();
        $iColumnSize = getMaxKeyLength($aTableFields);
        foreach ($aTableFields as $sFieldName => $aUncompatibilityFields) {
            if (false == empty($aUncompatibilityFields)) {
                $aCompatibilitiesToFile[] =
                    str_pad($sFieldName, ($iColumnSize + 2), " ") . ": " . implode(" ", $aUncompatibilityFields) . "\n";
            }
        }
        $sTableCompatibilitiesFilePath = $sCompatibilitiesFilePath.$sTableName.".yaml";
        file_put_contents($sTableCompatibilitiesFilePath, $aCompatibilitiesToFile);
        echo "Build thesaurus for uncompatibles fields for table ".$sTableName." in path: ".$sTableCompatibilitiesFilePath."\n";

        # List all blacklisted fields (percent data as value or field which after cleaning still have some uncompatibilities)
        $aBlacklistedFields = array();
        $aUncompatibleFields = array_keys(array_filter($aTableFields));
        foreach ($aUncompatibleFields as $sUncompatibleField) {
            $iNbUsing = 0;
            foreach ($aTableFields as $sFieldName => &$aUncompatibilityFields) {
                if (empty($aUncompatibilityFields)) {
                    continue;
                }
                if (false !== ($iKeyToUnblacklist = array_search($sUncompatibleField, $aUncompatibilityFields))) {
                    unset($aUncompatibilityFields[$iKeyToUnblacklist]);
                    $iNbUsing++;
                }
            }
            if ($iNbUsing > 0) {
                $aBlacklistedFields[] = $sUncompatibleField;
                unset($aTableFields[$sUncompatibleField]);
            }
        }
        $aBlacklistedFields = array_unique(array_merge($aBlacklistedFields, $aRateFields[$sTableName]));
        $sBlacklistedFieldsToFile = str_pad($sTableName, ($iTableColumnSize + 2), " ") . ": ".implode(" ", $aBlacklistedFields);
        file_put_contents($sBlacklistedFilePath, $sBlacklistedFieldsToFile."\n", FILE_APPEND);
    }
    echo "Build thesaurus for blacklisted fields by table in path: ".$sBlacklistedFilePath."\n";
}
