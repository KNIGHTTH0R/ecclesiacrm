<?php
/*******************************************************************************
 *
 *  filename    : Include/Header-functions.php
 *  website     : http://www.ecclesiacrm.com
 *  description : page header used for most pages
 *
 *  Copyright 2001-2004 Phillip Hullquist, Deane Barker, Chris Gebhardt, Michael Wilt
 *  Update 2018 Philippe Logel
 *
 *
 ******************************************************************************/

require_once 'Functions.php';

use EcclesiaCRM\Service\SystemService;
use EcclesiaCRM\dto\SystemURLs;
use EcclesiaCRM\Service\NotificationService;
use EcclesiaCRM\dto\SystemConfig;
use EcclesiaCRM\GroupQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use EcclesiaCRM\ListOptionQuery;
use EcclesiaCRM\MenuConfigQuery;
use EcclesiaCRM\UserConfigQuery;
use EcclesiaCRM\DepositQuery;
use EcclesiaCRM\PropertyQuery;
use EcclesiaCRM\Record2propertyR2p;
use EcclesiaCRM\Record2propertyR2pQuery;
use EcclesiaCRM\Group;
use EcclesiaCRM\Map\Record2propertyR2pTableMap;
use EcclesiaCRM\Map\PropertyTableMap;
use EcclesiaCRM\Map\PropertyTypeTableMap;
use EcclesiaCRM\Map\GroupTableMap;

function Header_system_notifications()
{
    if (NotificationService::hasActiveNotifications()) {
        ?>
        <div class="systemNotificationBar">
            <?php
            foreach (NotificationService::getNotifications() as $notification) {
                echo "<a href=\"" . $notification->link . "\">" . $notification->title . "</a>";
            } ?>
        </div>
        <?php
    }
}

function Header_head_metatag()
{
    global $sMetaRefresh, $sPageTitle;

    if (strlen($sMetaRefresh) > 0) {
        echo $sMetaRefresh;
    } ?>
    <title>EcclesiaCRM: <?= $sPageTitle ?></title>
    <?php
}

function Header_modals()
{
    ?>
    <!-- Issue Report Modal -->
    <div id="IssueReportModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div id="submitDiaglogStart">
                  <form name="issueReport">
                      <input type="hidden" name="pageName" value="<?= $_SERVER['SCRIPT_NAME'] ?>"/>
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title"><?= gettext('Issue Report!') ?></h4>
                      </div>
                      <div class="modal-body">
                          <div class="container-fluid">
                              <div class="row">
                                  <div class="col-xl-3">
                                      <label
                                              for="issueTitle"><?= gettext('Enter a Title for your bug / feature report') ?>
                                          : </label>
                                  </div>
                                  <div class="col-xl-3">
                                      <input type="text" name="issueTitle"  style="min-width: 100%;max-width: 100%;">
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-xl-3">
                                      <label
                                              for="issueDescription"><?= gettext('What were you doing when you noticed the bug / feature opportunity?') ?></label>
                                  </div>
                                  <div class="col-xl-3">
                                      <textarea rows="10" name="issueDescription" style="min-width: 100%;max-width: 100%;"></textarea>
                                  </div>
                              </div>
                          </div>
                          <ul>
                              <li><?= gettext("When you click \"submit,\" an error report will be posted to the EcclesiaCRM GitHub Issue tracker.") ?></li>
                              <li><?= gettext('Please do not include any confidential information.') ?></li>
                              <li><?= gettext('Some general information about your system will be submitted along with the request such as Server version and browser headers.') ?></li>
                              <li><?= gettext('No personally identifiable information will be submitted unless you purposefully include it.') ?></li>
                          </ul>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-primary" id="submitIssue"><?= gettext('Submit') ?></button>
                      </div>
                  </form>
              </div>
              <div id="submitDiaglogFinish">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?= gettext('Issue Report done!') ?></h4>
                </div>
                <div class="modal-body"><h2><?= _("Successfully submitted Issue") ?> <span id="issueSubmitSucces"></span></h2>
                <a href="" target="_blank" id="issueSubmitSuccesLink"><?= _("View Issue on GitHub")." : #" ?> <span id="issueSubmitSuccesLinkText"></span></a>
                <div class="modal-footer">
                          <button type="button" class="btn btn-primary" id="submitIssueDone"><?= gettext('OK') ?></button>
                </div>
                </div>              
              </div>
            </div>

        </div>
    </div>
    <!-- End Issue Report Modal -->

    <?php
}

function Header_body_scripts()
{
    global $localeInfo;
    $systemService = new SystemService(); ?>
    <script nonce="<?= SystemURLs::getCSPNonce() ?>">
        window.CRM = {
            root: "<?= SystemURLs::getRootPath() ?>",
            lang: "<?= $localeInfo->getLanguageCode() ?>",
            locale: "<?= $localeInfo->getLocale() ?>",
            shortLocale: "<?= $localeInfo->getShortLocale() ?>",
            currency: "<?= SystemConfig::getValue('sCurrency') ?>",
            maxUploadSize: "<?= $systemService->getMaxUploadFileSize(true) ?>",
            maxUploadSizeBytes: "<?= $systemService->getMaxUploadFileSize(false) ?>",
            datePickerformat:"<?= SystemConfig::getValue('sDatePickerPlaceHolder') ?>",
            timeEnglish:<?= (SystemConfig::getValue("sTimeEnglish"))?"true":"false" ?>,
            iDasbhoardServiceIntervalTime:"<?= SystemConfig::getValue('iDasbhoardServiceIntervalTime') ?>",
            showTooltip:<?= ($_SESSION['bShowTooltip'])?"true":"false" ?>,
            showCart:<?= ($_SESSION['user']->isShowCartEnabled())?"true":"false" ?>,
            bSidebarExpandOnHover:<?= ($_SESSION['bSidebarExpandOnHover'])?"true":"false" ?>,
            bSidebarCollapse:<?= ($_SESSION['bSidebarCollapse'])?"true":"false" ?>,
            iPersonId:<?= $_SESSION['user']->getPersonId() ?>,
            plugin: {
                dataTable : {
                   "language": {
                        "url": "<?= SystemURLs::getRootPath() ?>/locale/datatables/<?= $localeInfo->getDataTables() ?>.json"
                    },
                    responsive: true,
                    "dom": 'T<"clear">lfrtip',
                    "tableTools": {
                        "sSwfPath": "<?= SystemURLs::getRootPath() ?>/skin/adminlte/plugins/datatables/extensions/TableTools/swf/copy_csv_xls.swf"
                    }
                }
            },
            PageName:"<?= $_SERVER['PHP_SELF']?>"
        };
    </script>
    <script src="<?= SystemURLs::getRootPath() ?>/skin/js/CRMJSOM.js"></script>
    <?php
}

$security_matrix = GetSecuritySettings();

// return the security group to table
function GetSecuritySettings()
{
    $aSecurityListPrimal[] = 'bAdmin';
    $aSecurityListPrimal[] = 'bAddRecords';
    $aSecurityListPrimal[] = 'bEditRecords';
    $aSecurityListPrimal[] = 'bDeleteRecords';
    $aSecurityListPrimal[] = 'bMenuOptions';
    $aSecurityListPrimal[] = 'bManageGroups';
    $aSecurityListPrimal[] = 'bFinance';
    $aSecurityListPrimal[] = 'bNotes';
    $aSecurityListPrimal[] = 'bCommunication';
    $aSecurityListPrimal[] = 'bCanvasser';
    $aSecurityListPrimal[] = 'bSeePrivacyData';
    $aSecurityListPrimal[] = 'bShowTooltip';

    $ormSecGrpLists = UserConfigQuery::Create()
                        ->filterByPersonId(0)
                        ->filterByCat('SECURITY')
                        ->orderById()
                        ->find();

    foreach ($ormSecGrpLists as $ormSecGrpList) {
        $aSecurityListPrimal[] = $ormSecGrpList->getName();
    }

    asort($aSecurityListPrimal);

    $aSecurityListFinal = array('bALL');
    for ($i = 0; $i < count($aSecurityListPrimal); $i++) {
        if (array_key_exists($aSecurityListPrimal[$i], $_SESSION) && $_SESSION[$aSecurityListPrimal[$i]]) {
            $aSecurityListFinal[] = $aSecurityListPrimal[$i];
        }
    }

    return $aSecurityListFinal;
}

function addMenu($menu)
{
    global $security_matrix;

    $ormMenus = MenuConfigQuery::Create()
                        ->filterByParent('%'.$menu.'%', Criteria::LIKE)
                        ->filterByActive(1);

    $firstTime = 1;
    for ($i = 0; $i < count($security_matrix); $i++) {
        if ($firstTime) {
            $ormMenus->filterBySecurityGroup($security_matrix[$i]);
        } else {
            $ormMenus->_or()->filterBySecurityGroup($security_matrix[$i]);
        }
        $firstTime = 0;
    }

    $ormMenus->orderBySortOrder()
                        ->find();

    $item_cnt = count($ormMenus);

    $idx = 1;
    $ptr = 1;
    foreach ($ormMenus as $ormMenu) {
        if (addMenuItem($ormMenu, $idx)) {
            if ($ptr == $item_cnt) {
                $idx++;
            }
            $ptr++;
        } else {
            $item_cnt--;
        }
    }
}

function addMenuItem($ormMenu, $mIdx)
{
    global $security_matrix;
    $maxStr = 25;

    $link = ($ormMenu->getURI() == '') ? '' : SystemURLs::getRootPath() . '/' . $ormMenu->getURI();
    $text = $ormMenu->getStatus();
    if (!is_null($ormMenu->getSessionVar())) {
        if (($link > '') && ($ormMenu->getSessionVarInURI()) && isset($_SESSION[$ormMenu->getSessionVar()])) {
            if (strstr($link, '?') && true) {
                $cConnector = '&';
            } else {
                $cConnector = '?';
            }
            $link .= $cConnector . $ormMenu->getURLParmName() . '=' . $_SESSION[$ormMenu->getSessionVar()];
        }
        if (($text > '') && ($ormMenu->getSessionVarInText()) && isset($_SESSION[$ormMenu->getSessionVar()])) {
            $text .= ' ' . $_SESSION[$ormMenu->getSessionVar()];
        }
    }
    if ($ormMenu->getMenu()) {
        $ormItemCnt = MenuConfigQuery::Create()
                        ->filterByParent('%'.$ormMenu->getName().'%', Criteria::LIKE)
                        ->filterByActive(1);

        $firstTime = 1;
        for ($i = 0; $i < count($security_matrix); $i++) {
            if ($firstTime) {
                $ormItemCnt->filterBySecurityGroup($security_matrix[$i]);
            } else {
                $ormItemCnt->_or()->filterBySecurityGroup($security_matrix[$i]);
            }
            $firstTime = 0;
        }

        $ormItemCnt->orderBySortOrder()
                        ->find();

        $numItems = count($ormItemCnt);
    }
    
    if ($ormMenu->getName() == 'calendar') {
    ?>
       <li class="treeview">
            <a href="<?= SystemURLs::getRootPath() . '/' . $ormMenu->getURI() ?>">
            <i class='fa <?= $ormMenu->getIcon() ?> fa-calendar pull-left"'></i>
            <span>
              <?= gettext($ormMenu->getContent()) ?>
            </span>
          </a>
        </li>
    <?php
    
    } else 
    if ($ormMenu->getName() == 'events') {
    ?>
       <li class="treeview">
            <a href="<?= SystemURLs::getRootPath() . '/' . $ormMenu->getURI() ?>">
              <i class='fa <?= $ormMenu->getIcon() ?> fa-calendar pull-right"'>            </i>
              <span>
                 <?= gettext($ormMenu->getContent()) ?>
              </span>
              <i class="fa fa-angle-left pull-right"></i>
              <small class='label bg-blue pull-right' id='AnniversaryNumber'>0</small>
              <small class='label bg-red pull-right' id='BirthdateNumber'>0</small>
              <small class='label bg-yellow pull-right' id='EventsNumber'>0</small>
            </a>
            <ul class="treeview-menu">
              <?php
                echo "\n";
                addMenu($ormMenu->getName());
              ?>
            </ul>
        </li>
        
    <?php
    
    } else if (!($ormMenu->getMenu()) || ($numItems > 0)) {
        if ($link) {
            if ($ormMenu->getName() != 'sundayschool-dash' && $ormMenu->getName() != 'listgroups' && $ormMenu->getName() != 'listgroups') { // HACK to remove the sunday school 2nd dashboard and groups
                if ($ormMenu->getContent() == 'Edit Deposit Slip') {           
                   $deposit = DepositQuery::Create()->findOneById($_SESSION['iCurrentDeposit']);
                }
                
                if ($ormMenu->getContent() == 'Edit Deposit Slip') {
                  if (empty($deposit)) {
                    echo "\n<li><a href='$link' style='display: none;' class='deposit-current-deposit-item'";
                  } else {
                    echo "\n<li><a href='$link' class='deposit-current-deposit-item'>";
                  }
                } else {
                  echo "\n<li><a href='$link'>";
                }
                
                if ($ormMenu->getIcon() != '') {
                    echo '<i class="fa ' . $ormMenu->getIcon() . '"></i>';
                }
                if ($ormMenu->getParent() != 'root') {
                    echo '<i class="fa fa-angle-double-right"></i> ';
                }
                if ($ormMenu->getParent() == 'root') {
                    echo '<span>' . gettext($ormMenu->getContent()) . '</span></a>';
                } else {
                    echo gettext($ormMenu->getContent());
                    
                    if ($ormMenu->getContent() == 'Edit Deposit Slip') {
                      echo ' : <small class="badge pull-right bg-blue current-deposit-item"> #'.$_SESSION['iCurrentDeposit']. "</small>\n";
                    }
                    
                    echo '</a>';
                }
            } elseif ($ormMenu->getName() == 'listgroups') {
                echo "\n<li><a href='" . SystemURLs::getRootPath() . "/GroupList.php'><i class='fa fa-angle-double-right'></i>" . gettext('List Groups') . '</a></li>';

                $listOptions = ListOptionQuery::Create()
                    ->filterById(3)
                    ->orderByOptionName()
                    ->find();

                foreach ($listOptions as $listOption) {
                    if ($listOption->getOptionId() != 4) {// we avoid the sundaySchool, it's done under
                        $groups=GroupQuery::Create()
                            ->filterByType($listOption->getOptionId())
                            ->orderByName()
                            ->find();

                        if (count($groups)>0) {// only if the groups exist : !empty doesn't work !
                            echo "\n<li><a href='#'><i class='fa fa-user-o'></i>" . $listOption->getOptionName(). '</a>';
                            echo '<ul class="treeview-menu">';

                            foreach ($groups as $group) {
                                $str = $group->getName();
                                if (strlen($str)>$maxStr) {
                                    $str = substr($str, 0, $maxStr-3)." ...";
                                }

                                echo "\n<li><a href='" . SystemURLs::getRootPath() . '/GroupView.php?GroupID=' . $group->getID() . "'><i class='fa fa-angle-double-right'></i> " .$str. '</a></li>';
                            }
                            echo '</ul></li>';
                        }
                    }
                }

                // now we're searching the unclassified groups
                $groups=GroupQuery::Create()
                            ->filterByType(0)
                            ->orderByName()
                            ->find();

                if (count($groups)>0) {// only if the groups exist : !empty doesn't work !
                    echo "\n<li><a href='#'><i class='fa fa-user-o'></i>" . gettext("Unassigned"). '</a>';
                    echo "\n<ul class='treeview-menu'>";

                    foreach ($groups as $group) {
                        echo "\n<li><a href='" . SystemURLs::getRootPath() . '/GroupView.php?GroupID=' . $group->getID() . "'><i class='fa fa-angle-double-right'></i> " . $group->getName() . '</a></li>';
                    }
                    echo "\n</ul>";
                }
            }
        } else {
            echo "<li class=\"treeview\">\n";
            echo "    <a href=\"#\">\n";
            if ($ormMenu->getIcon() != '') {
                echo '<i class="fa ' . $ormMenu->getIcon() . "\"></i>\n";
            }
            echo '<span>' . gettext($ormMenu->getContent()) . "</span>\n";
            echo "<i class=\"fa fa-angle-left pull-right\"></i>\n";

            if ($ormMenu->getName() == 'deposit') {
                $deposit = DepositQuery::Create()->findOneById($_SESSION['iCurrentDeposit']);
                $deposits = DepositQuery::Create()->find();
                
                $numberDeposit = 0;
                
                if (!empty($deposits)) {
                  $numberDeposit = $deposits->count();
                }
                
                echo '<small class="badge pull-right bg-green count-deposit">'.$numberDeposit. "</small>".((!empty($deposit))?('<small class="badge pull-right bg-blue current-deposit" data-id="'.$_SESSION['iCurrentDeposit'].'">'.gettext("Current")." : ".$_SESSION['iCurrentDeposit'] . "</small>"):"")."\n";
            } ?>  </a>
      <ul class="treeview-menu">
      <?php
            //Get the Properties assigned to all the sunday Group
            $ormAssignedProperties = Record2propertyR2pQuery::Create()
                            ->addJoin(Record2propertyR2pTableMap::COL_R2P_PRO_ID,PropertyTableMap::COL_PRO_ID,Criteria::LEFT_JOIN)
                            ->addJoin(PropertyTableMap::COL_PRO_PRT_ID,PropertyTypeTableMap::COL_PRT_ID,Criteria::LEFT_JOIN)
                            ->addJoin(Record2propertyR2pTableMap::COL_R2P_RECORD_ID,GroupTableMap::COL_GRP_ID,Criteria::LEFT_JOIN)
                            ->addAsColumn('ProName',PropertyTableMap::COL_PRO_NAME)
                            ->addAsColumn("GroupId",GroupTableMap::COL_GRP_ID)
                            ->addAsColumn("GroupName",GroupTableMap::COL_GRP_NAME)
                            ->where(PropertyTableMap::COL_PRO_CLASS." = 'm' AND ".GroupTableMap::COL_GRP_TYPE." = '4' AND ". PropertyTypeTableMap::COL_PRT_NAME." = 'MENU'")
                            ->addAscendingOrderByColumn('ProName')
                            ->addAscendingOrderByColumn('groupName')
                            ->find();

            //Get the sunday groups not assigned by properties
            $ormWithoutAssignedProperties = GroupQuery::Create()
                            ->addJoin(GroupTableMap::COL_GRP_ID,Record2propertyR2pTableMap::COL_R2P_RECORD_ID,Criteria::LEFT_JOIN)
                            ->addJoin(Record2propertyR2pTableMap::COL_R2P_PRO_ID,PropertyTableMap::COL_PRO_ID,Criteria::LEFT_JOIN)
                            ->addJoin(PropertyTableMap::COL_PRO_PRT_ID,PropertyTypeTableMap::COL_PRT_ID,Criteria::LEFT_JOIN)
                            ->addAsColumn('PrtName',PropertyTypeTableMap::COL_PRT_NAME)
                            ->addAsColumn("GroupId",GroupTableMap::COL_GRP_ID)
                            ->addAsColumn("GroupName",GroupTableMap::COL_GRP_NAME)
                            ->where("((".Record2propertyR2pTableMap::COL_R2P_RECORD_ID." IS NULL) OR (".PropertyTypeTableMap::COL_PRT_NAME." != 'Menu')) AND ".GroupTableMap::COL_GRP_TYPE." = '4'")
                            ->addAscendingOrderByColumn('groupName')
                            ->find();

            if ($ormMenu->getName() == 'sundayschool') {
                echo "\n<li><a href='" . SystemURLs::getRootPath() . "/sundayschool/SundaySchoolDashboard.php'><i class='fa fa-angle-double-right'></i>" . gettext('Dashboard') . '</a></li>';

                $property = '';
                foreach ($ormAssignedProperties as $ormAssignedProperty) {
                    if ($ormAssignedProperty->getProName() != $property) {
                        if (!empty($property)) {
                            echo '</ul></li>';
                        }

                        echo '<li><a href="#"><i class="fa fa-user-o"></i><span>'.$ormAssignedProperty->getProName().'</span></a>';
                        echo '<ul class="treeview-menu">';


                        $property = $ormAssignedProperty->getProName();
                    }

                    $str = gettext($ormAssignedProperty->getGroupName());
                    if (strlen($str)>$maxStr) {
                        $str = substr($str, 0, $maxStr-3)." ...";
                    }

                    echo "\n<li><a href='" . SystemURLs::getRootPath() . '/sundayschool/SundaySchoolClassView.php?groupId=' . $ormAssignedProperty->getGroupId() . "'><i class='fa fa-angle-double-right'></i> " .$str. '</a></li>';
                }

                if (!empty($property)) {
                    echo '</ul></li>';
                }

                // the non assigned group to a group property
                foreach ($ormWithoutAssignedProperties as $ormWithoutAssignedProperty) {
                    $str = gettext($ormWithoutAssignedProperty->getGroupName());
                    if (strlen($str)>$maxStr) {
                        $str = substr($str, 0, $maxStr-3)." ...";
                    }

                    echo "\n<li><a href='" . SystemURLs::getRootPath() . '/sundayschool/SundaySchoolClassView.php?groupId=' . $ormWithoutAssignedProperty->getGroupId() . "'><i class='fa fa-angle-double-right'></i> " . $str . '</a></li>';
                  
                }
            }
        }
        if (($ormMenu->getMenu()) && ($numItems > 0)) {
            echo "\n";
            addMenu($ormMenu->getName());
            echo "</ul>\n</li>\n";
        } else {
            echo "</li>\n";
        }

        return true;
    } else {
        return false;
    }
}

?>
