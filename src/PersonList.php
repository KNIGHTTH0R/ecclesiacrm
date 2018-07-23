<?php
require 'Include/Config.php';
require 'Include/Functions.php';

use EcclesiaCRM\dto\SystemConfig;
use EcclesiaCRM\PersonQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use EcclesiaCRM\Utils\InputUtils;
use EcclesiaCRM\dto\SystemURLs;

$sMode = 'Active';
// Filter received user input as needed
if (isset($_GET['mode'])) {
    $sMode = InputUtils::LegacyFilterInput($_GET['mode']);
}

if (strtolower($sMode) == 'gdrp') {
  if (!$_SESSION['user']->isGdrpDpoEnabled()) {
    Redirect("Menu.php");
    exit;
  }

   $time = new DateTime('now');
   $newtime = $time->modify('-2 year')->format('Y-m-d');
   
   $persons = PersonQuery::create()
        ->filterByDateDeactivated($newtime, Criteria::LESS_THAN)
            ->orderByLastName()
            ->find();
            
} else if (strtolower($sMode) == 'inactive') {
    $persons = PersonQuery::create()
        ->filterByDateDeactivated(null, Criteria::ISNOTNULL)
            ->orderByLastName()
            ->find();
} else {
    $sMode = 'Active';
    $persons = PersonQuery::create()
        ->filterByDateDeactivated(null)
            ->orderByLastName()
            ->find();
}

// Set the page title and include HTML header
$sPageTitle = gettext(ucfirst($sMode)) . ' ' . gettext('Person List');
require 'Include/Header.php'; ?>

<div class="pull-right">
  <a class="btn btn-success" role="button" href="PersonEditor.php"> 
    <span class="fa fa-plus" aria-hidden="true"></span><?= gettext('Add New Person') ?>
  </a>
</div>
<p><br/><br/></p>
<div class="box">
    <div class="box-body">
        <table id="personlist" class="table table-striped table-bordered data-table" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= gettext('Name') ?></th>
                <th><?= gettext('First Name') ?></th>
                <th><?= gettext('Address') ?></th>
                <th><?= gettext('Home Phone') ?></th>
                <th><?= gettext('Cell Phone') ?></th>
                <th><?= gettext('email') ?></th>
                <th><?= gettext('Created') ?></th>
                <th><?= gettext('Edited') ?></th>
            <?php if (strtolower($sMode) == 'gdrp') { ?>
                <th><?= gettext('Deactivation date') ?></th>
                <th><?= gettext('Remove') ?></th>
            <?php } ?>
            </tr>
            </thead>
            <tbody>

            <!--Populate the table with Person details -->
          <?php 
            foreach ($persons as $person) {
          ?>
            <tr>
                <td><a href='PersonView.php?PersonID=<?= $person->getId() ?>'>
                        <span class="fa-stack">
                            <i class="fa fa-square fa-stack-2x"></i>
                            <i class="fa fa-search-plus fa-stack-1x fa-inverse"></i>
                        </span>
                    </a>
                    <a href='PersonEditor.php?PersonID=<?= $person->getId() ?>'>
                        <span class="fa-stack">
                            <i class="fa fa-square fa-stack-2x"></i>
                            <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
                        </span>
                    </a><?= $person->getLastName() ?>
                </td>
                <td> <?= $person->getFirstName() ?></td>
                <?php    
                if ($_SESSION['user']->isSeePrivacyDataEnabled()) {
                ?>
                  <td> <?= $person->getAddress() ?></td>
                  <td><?= $person->getHomePhone() ?></td>
                  <td><?= $person->getCellPhone() ?></td>
                  <td><?= $person->getEmail() ?></td>
                  <td><?= date_format($person->getDateEntered(), SystemConfig::getValue('sDateFormatLong')) ?></td>
                  <td><?= date_format($person->getDateLastEdited(), SystemConfig::getValue('sDateFormatLong')) ?></td>
                <?php
                } else {
                ?>
                  <td> <?= gettext('Private Data') ?></td>
                  <td> <?= gettext('Private Data') ?></td>
                  <td> <?= gettext('Private Data') ?></td>
                  <td> <?= gettext('Private Data') ?></td>
                  <td> <?= gettext('Private Data') ?></td>
                <?php
                }
              if (strtolower($sMode) == 'gdrp') { ?>
                  <td> <?= date_format($person->getDateDeactivated(), SystemConfig::getValue('sDateFormatLong')) ?></td>
                  <td><a class="btn btn-danger remove-property-btn" data-person_id="<?= $person->getId() ?>"><?= gettext("Remove") ?></a></td>
              <?php 
                } 
           }
        ?>
            </tr>
            </tbody>
        </table>
        <?php if (strtolower($sMode) == 'gdrp') { ?>        
        <a class="btn btn-danger <?= ($persons->count() == 0)?"disabled":"" ?>" id="remove-all"><?= gettext("Remove All") ?></a>
        <?php } ?>
    </div>
</div>

<script nonce="<?= SystemURLs::getCSPNonce() ?>" >
  $(document).ready(function () {
    $('#personlist').DataTable(window.CRM.plugin.dataTable);
  });
</script>

<script src="<?= SystemURLs::getRootPath() ?>/skin/js/PersonList.js" ></script>

<?php
require 'Include/Footer.php';
?>