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

// All available reports in Google Adwords
$aUrl = array(
    "ACCOUNT_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/account-performance-report",
    "AD_CUSTOMIZERS_FEED_ITEM_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/ad-customizers-feed-item-report",
    "AD_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/ad-performance-report",
    "ADGROUP_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/adgroup-performance-report",
    "AGE_RANGE_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/age-range-performance-report",
    "AUDIENCE_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/audience-performance-report",
    "AUTOMATIC_PLACEMENTS_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/automatic-placements-performance-report",
    "BID_GOAL_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/bid-goal-performance-report",
    "BUDGET_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/budget-performance-report",
    "CALL_METRICS_CALL_DETAILS_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/call-metrics-call-details-report",
    "CAMPAIGN_AD_SCHEDULE_TARGET_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-ad-schedule-target-report",
    "CAMPAIGN_LOCATION_TARGET_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-location-target-report",
    "CAMPAIGN_NEGATIVE_KEYWORDS_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-negative-keywords-performance-report",
    "CAMPAIGN_NEGATIVE_LOCATIONS_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-negative-locations-report",
    "CAMPAIGN_NEGATIVE_PLACEMENTS_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-negative-placements-performance-report",
    "CAMPAIGN_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-performance-report",
    "CAMPAIGN_PLATFORM_TARGET_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-platform-target-report",
    "CAMPAIGN_SHARED_SET_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/campaign-shared-set-report",
    "CLICK_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/click-performance-report",
    "CREATIVE_CONVERSION_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/creative-conversion-report",
    "CRITERIA_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/criteria-performance-report",
    "DESTINATION_URL_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/destination-url-report",
    "DISPLAY_KEYWORD_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/display-keyword-performance-report",
    "DISPLAY_TOPICS_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/display-topics-performance-report",
    "FINAL_URL_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/final-url-report",
    "GENDER_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/gender-performance-report",
    "GEO_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/geo-performance-report",
    "KEYWORDLESS_CATEGORY_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/keywordless-category-report",
    "KEYWORDLESS_QUERY_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/keywordless-query-report",
    "KEYWORDS_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/keywords-performance-report",
    "LABEL_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/label-report",
    "PAID_ORGANIC_QUERY_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/paid-organic-query-report",
    "PLACEHOLDER_FEED_ITEM_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/placeholder-feed-item-report",
    "PLACEHOLDER_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/placeholder-report",
    "PLACEMENT_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/placement-performance-report",
    "PRODUCT_PARTITION_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/product-partition-report",
    "SEARCH_QUERY_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/search-query-performance-report",
    "SHARED_SET_CRITERIA_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/shared-set-criteria-report",
    "SHARED_SET_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/shared-set-report",
    "SHOPPING_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/shopping-performance-report",
    "URL_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/url-performance-report",
    "USER_AD_DISTANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/user-ad-distance-report",
    "VIDEO_PERFORMANCE_REPORT" => "https://developers.google.com/adwords/api/docs/appendix/reports/video-performance-report"
);

const ADWORDS_API_VERSION = "v201601";

const YAML_DIRECTORY = "adwords";
const YAML_COMPATIBILITY_DIR = "compatibility"; // For each tables, list all fields with incompatibility with others (from the more inconsistent at least )
const YAML_TABLES = "tables.yaml"; // List all tables available, for each gives fields inside
const YAML_BLACKLIST_FIELDS = "blacklisted_fields.yaml"; // For each tables, list all fields to exclude
const YAML_FIELDS = "fields.yaml"; // List all fields available, for each gives type of the data
const YAML_KEYS = "keys.yaml"; // List all tables available, for each gives segmented fields
const YAML_EXTRA = "extra.yaml"; // List all fields available, for each gives more informations about it

const XPATH_FIELD = ".devsite-article-body tbody tr";
const XPATH_FIELD_NAME = "h3";
const XPATH_FIELD_INFO = ".nested td";
const XPATH_FIELD_EXTRA = "p";
const XPATH_FIELD_ENUM = ".expandable .nested code";
const XPATH_FIELD_COMPATIBILITY = ".expandable div code";

$aFields = array();
$aPercents = array();
$aKeys = array();
$aExtra = array();
$aTables = array();
$aCompatibility = array();
$iTableNb = count($aUrl);
$iTablePos = 1;

# For each Url, fetch and parse Google Adwords page
foreach ($aUrl as $sTableName => $sAdwordsUrl)
{
    $aTables[$sTableName] = array();
    $aKeys[$sTableName] = array();
    $aCompatibility[$sTableName] = array();

    echo "Fecth ".$iTablePos."/".$iTableNb.": ".$sAdwordsUrl."\n";

    $html = HtmlParser::from_file($sAdwordsUrl);
    foreach ($html->find(XPATH_FIELD) as $oField)
    {
        if (null !== ($oFieldName = $oField->find(XPATH_FIELD_NAME, 0))) {
            # Field name
            $sFieldName = trim($oFieldName->text);
            $oFieldInfos = $oField->find(XPATH_FIELD_INFO);
            foreach ($oFieldInfos as $iIndex => $oFieldInfo)
            {
                if ("Type" == $oFieldInfo->text) {
                    if (false == isset($aFields[$sFieldName])) {
                        # Type of field
                        $aFields[$sFieldName] = trim($oFieldInfos[($iIndex + 1)]->text);
                        # Add enum values ?
                        if (null != ($oFieldEnums = $oField->find(XPATH_FIELD_ENUM))) {
                            $aEnum = array();
                            foreach ($oFieldEnums as $oFieldEnum)
                            {
                                $aEnum[] = trim($oFieldEnum->text);
                            }
                            $aFields[$sFieldName] .= " (".implode(" ", $aEnum).")";
                        }
                    }
                } elseif ("Behavior" == $oFieldInfo->text && "Segment" == $oFieldInfos[($iIndex + 1)]->text) {
                    # Field can be a structuring key
                    $aKeys[$sTableName][] = $sFieldName;
                }

                # This field has uncompatibilities with others ?
                if (false == isset($aCompatibility[$sTableName][$sFieldName])) {
                    $aCompatibility[$sTableName][$sFieldName] = array();

                    $oFieldCompatibilities = $oField->find(XPATH_FIELD_COMPATIBILITY);
                    foreach ($oFieldCompatibilities as $oFieldCompatibility)
                    {
                        $aCompatibility[$sTableName][$sFieldName][] = $oFieldCompatibility->text;
                    }
                }

            }
            if (false == isset($aExtra[$sFieldName])) {
                # Give a description for this field
                $aExtra[$sFieldName] = trim(strtok($oField->find(XPATH_FIELD_EXTRA, 0)->text, "\n"));
                if (false !== stripos($aExtra[$sFieldName], 'percentage return')) {
                    $aPercents[$sFieldName] = 1;
                }
            }
            # Add this field in this table
            $aTables[$sTableName][] = $sFieldName;
        }
    }
    $iTablePos++;
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

# Create thesaurus in Yaml format

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
        $sBlacklistedFields = "";
        $aUncompatibleFields = array_keys(array_filter($aTableFields));
        foreach ($aUncompatibleFields as $sUncompatibleField) {
            $iNbUsing = 0;
            if (false == isset($aPercents[$sUncompatibleField])) {
                foreach ($aTableFields as $sFieldName => &$aUncompatibilityFields) {
                    if (empty($aUncompatibilityFields)) {
                        continue;
                    }
                    if (false !== ($iKeyToUnblacklist = array_search($sUncompatibleField, $aUncompatibilityFields))) {
                        unset($aUncompatibilityFields[$iKeyToUnblacklist]);
                        $iNbUsing++;
                    }
                }
            } else {
                $iNbUsing++;
            }
            if ($iNbUsing > 0) {
                $sBlacklistedFields .= " ".$sUncompatibleField;
                unset($aTableFields[$sUncompatibleField]);
            }
        }
        $sBlacklistedFieldsToFile = str_pad($sTableName, ($iTableColumnSize + 2), " ") . ": ".trim($sBlacklistedFields);
        file_put_contents($sBlacklistedFilePath, $sBlacklistedFieldsToFile."\n", FILE_APPEND);
    }
    echo "Build thesaurus for blacklisted fields by table in path: ".$sBlacklistedFilePath."\n";
}