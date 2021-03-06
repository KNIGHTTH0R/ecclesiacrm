<?php

/*******************************************************************************
 *
 *  filename    : dashboard.php
 *  last change : 2018-07-13
 *  description : manage the full GDPR
 *
 *  http://www.ecclesiacrm.com/
 *
 *  This code is under copyright not under MIT Licence
 *  copyright   : 2018 Philippe Logel all right reserved not MIT licence
 *                This code can't be incoprorated in another software without authorizaion
 *
 ******************************************************************************/

require $sRootDocument . '/Include/Header.php';
?>

<div class="box box-primary box-body">
  <div class="row ">
      <div class="col-sm-2" style="vertical-align: middle;">
         <a class="btn btn-app" href="<?= $sRootPath ?>/Reports/GDPR/GDPRListExport.php"><i class="fa fa-print"></i> <?= _("Printable Page") ?></a>
      </div>
      <div class="col-sm-10" style="vertical-align: middle;">
        <table>
          <tr>
            <td><label><?= _("GDPR DPO Signer") ?></label></td><td>&nbsp;:&nbsp;</td><td><?= $gdprSigner ?></td>
          </tr>
          <tr>
            <td><label><?= _("GDPR DPO Signer Email") ?></label></td><td>&nbsp;:&nbsp;</td><td><?= $gdprSignerEmail ?></td>
          </tr>
        </table>
      </div>
    </div>
</div>

<div class="box box-body">
<div class="box-header with-border">
  <i class="fa fa-user"></i>
  <h3 class="box-title"><?= _("GDPR Person status") ?></h3>
</div>
  <table class="table table-striped table-bordered" id="GDRP-Table" cellpadding="5" cellspacing="0"  width="100%"></table>
</div>

<script src="<?= $sRootPath ?>/skin/js/gdpr/GDRPDashboard.js" ></script>

<?php require $sRootDocument . '/Include/Footer.php'; ?>