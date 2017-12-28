<?php
/*******************************************************************************
 *
 *  filename    : NoteEditor.php
 *  last change : 2003-01-07
 *  website     : http://www.ecclesiacrm.com
 *  copyright   : Copyright 2001, 2002 Deane Barker
  *
 ******************************************************************************/

//Include the function library
require 'Include/Config.php';
require 'Include/Functions.php';

use EcclesiaCRM\Note;
use EcclesiaCRM\NoteQuery;
use EcclesiaCRM\Utils\InputUtils;
use EcclesiaCRM\dto\SystemURLs;

// Security: User must have Notes permission
// Otherwise, re-direct them to the main menu.
if (!$_SESSION['bNotes']) {
    Redirect('Menu.php');
    exit;
}

//Set the page title
$sPageTitle = gettext('Note Editor');

if (isset($_GET['PersonID'])) {
    $iPersonID = InputUtils::LegacyFilterInput($_GET['PersonID'], 'int');
} else {
    $iPersonID = 0;
}

if (isset($_GET['FamilyID'])) {
    $iFamilyID = InputUtils::LegacyFilterInput($_GET['FamilyID'], 'int');
} else {
    $iFamilyID = 0;
}

//To which page do we send the user if they cancel?
if ($iPersonID > 0) {
    $sBackPage = 'PersonView.php?PersonID='.$iPersonID;
} else {
    $sBackPage = 'FamilyView.php?FamilyID='.$iFamilyID;
}

//Has the form been submitted?
if (isset($_POST['Submit'])) {
    //Initialize the ErrorFlag
    $bErrorFlag = false;

    //Assign all variables locally
    $iNoteID = InputUtils::LegacyFilterInput($_POST['NoteID'], 'int');
    $sNoteText = InputUtils::FilterHTML($_POST['NoteText'], 'htmltext');

    //If they didn't check the private box, set the value to 0
    if (isset($_POST['Private'])) {
        $bPrivate = 1;
    } else {
        $bPrivate = 0;
    }

    //Did they enter text for the note?
    if ($sNoteText == '') {
        $sNoteTextError = '<br><span style="color: red;">You must enter text for this note.</span>';
        $bErrorFlag = true;
    }

    //Were there any errors?
    if (!$bErrorFlag) {
        //Are we adding or editing?
        if ($iNoteID <= 0) {
            $note = new Note();
            $note->setPerId($iPersonID);
            $note->setFamId($iFamilyID);
            $note->setPrivate($bPrivate);
            $note->setText($sNoteText);
            $note->setType('note');
            $note->setEntered($_SESSION['iUserID']);
            $note->save();
        } else {
            $note = NoteQuery::create()->findPk($iNoteID);
            $note->setPrivate($bPrivate);
            $note->setText($sNoteText);
            $note->setDateLastEdited(new DateTime());
            $note->setEditedBy($_SESSION['iUserID']);
            $note->save();
        }

        //Send them back to whereever they came from
        Redirect($sBackPage);
    }
} else {
    //Are we adding or editing?
    if (isset($_GET['NoteID'])) {
        //Get the NoteID from the querystring
        $iNoteID = InputUtils::LegacyFilterInput($_GET['NoteID'], 'int');
        $dbNote = NoteQuery::create()->findPk($iNoteID);

        //Assign everything locally
        $sNoteText = $dbNote->getText();
        $bPrivate = $dbNote->getPrivate();
        $iPersonID = $dbNote->getPerId();
        $iFamilyID = $dbNote->getFamId();
    }
}
require 'Include/Header.php';

?>
<form method="post">
  <div class="box box-primary">
    <div class="box-body">

      <p align="center">
        <input type="hidden" name="PersonID" value="<?= $iPersonID ?>">
        <input type="hidden" name="FamilyID" value="<?= $iFamilyID ?>">
        <input type="hidden" name="NoteID" value="<?= $iNoteID ?>">
        <textarea id="NoteText" name="NoteText" style="width: 100%;min-height: 300px;" rows="40"><?= $sNoteText ?></textarea>
        <?= $sNoteTextError ?>
      </p>

      <p align="center">
        <input type="checkbox" value="1" name="Private" <?php if ($bPrivate != 0) {
    echo 'checked';
} ?>>&nbsp;<?= gettext('Private') ?>
      </p>
    </div>
  </div>
  <p align="center">
    <input type="submit" class="btn btn-success" name="Submit" value="<?= gettext('Save') ?>">
    &nbsp;
    <input type="button" class="btn" name="Cancel" value="<?= gettext('Cancel') ?>" onclick="javascript:document.location='<?= $sBackPage ?>';">

  </p>
</form>

<?php require 'Include/Footer.php' ?>

<script src="<?= SystemURLs::getRootPath() ?>/skin/external/ckeditor/ckeditor.js"></script>

<script nonce="<?= SystemURLs::getCSPNonce() ?>">
  CKEDITOR.replace('NoteText',{
    customConfig: '<?= SystemURLs::getRootPath() ?>/skin/js/ckeditor/note_editor_config.js',
    language : window.CRM.lang
  });
</script>
