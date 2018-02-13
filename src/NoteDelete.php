<?php
/*******************************************************************************
 *
 *  filename    : NoteDelete.php
 *  last change : 2003-01-07
 *  website     : http://www.ecclesiacrm.com
 *  copyright   : Copyright 2001, 2002 Deane Barker
  *
 ******************************************************************************/

//Include the function library
require 'Include/Config.php';
require 'Include/Functions.php';

use EcclesiaCRM\NoteQuery;
use EcclesiaCRM\Utils\InputUtils;


//Set the page title
$sPageTitle = gettext('Document Delete Confirmation');

//Get the NoteID from the querystring
$iNoteID = InputUtils::LegacyFilterInput($_GET['NoteID'], 'int');

//Get the data on this note
$sSQL = 'SELECT * FROM note_nte WHERE nte_ID = '.$iNoteID;
$rsNote = RunQuery($sSQL);
extract(mysqli_fetch_array($rsNote));

//If deleting a note for a person, set the PersonView page as the redirect
if ($nte_per_ID > 0) {
    $sReroute = 'PersonView.php?PersonID='.$nte_per_ID;
}

//If deleting a note for a family, set the FamilyView page as the redirect
elseif ($nte_fam_ID > 0) {
    $sReroute = 'FamilyView.php?FamilyID='.$nte_fam_ID;
}

$iCurrentFamID = $_SESSION['user']->getPerson()->getFamId();

// Security: User must have Notes permission
// Otherwise, re-direct them to the main menu.
if (!($_SESSION['bNotes']  || $nte_per_ID == $_SESSION['user']->getPersonId() || $nte_fam_ID == $iCurrentFamID)) {
    Redirect('Menu.php');
    exit;
}


//Do we have confirmation?
if (isset($_GET['Confirmed'])) {
    $note = NoteQuery::create()->findPk($iNoteID);
    $note->delete();

    //Send back to the page they came from
    Redirect($sReroute);
}

require 'Include/Header.php';

?>
<div class="box box-warning">
  <div class="box-header with-border">
	<?= gettext('Please confirm deletion of this document') ?>:
  </div>
  <div class="box-body">
    <?= $nte_Text ?>
  </div>
  <div class="box-footer">
    <a class="btn btn-default" href="<?php echo $sReroute ?>"><?= gettext('Cancel') ?></a>
  	<a class="btn btn-danger" href="NoteDelete.php?Confirmed=Yes&NoteID=<?php echo $iNoteID ?>"><?= gettext('Yes, delete this record') ?></a> <?= gettext('(this action cannot be undone)') ?>
  </div>

<?php require 'Include/Footer.php' ?>
