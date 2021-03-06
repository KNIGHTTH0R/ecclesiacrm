function contentExists(contentUrl, callback) {
    $.ajax({
        method :"HEAD",
        url: contentUrl,
        processData: false,
        global:false,
        success: function(data, textStatus, jqXHR){
            callback(true, data, textStatus, jqXHR);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            callback(false, jqXHR, textStatus, errorThrown);
        }
    });
}

$('.delete-person').click(function (event) {
    event.preventDefault();
    var thisLink = $(this);
    bootbox.confirm({
        title:i18next.t("Delete this person?"),
        message: i18next.t("Do you want to delete this person? This cannot be undone.") + " <b>" + thisLink.data('person_name')+'</b>',
        buttons: {
            cancel: {
                className: 'btn-primary',
                label: '<i class="fa fa-times"></i>' + i18next.t("Cancel")
            },
            confirm: {
                className: 'btn-danger',
                label: '<i class="fa fa-trash-o"></i>' + i18next.t("Delete")
            }
        },
        callback: function (result) {
            if(result) {
                $.ajax({
                    type: 'DELETE',
                    url: window.CRM.root + '/api/persons/' + thisLink.data('person_id'),
                    dataType: 'json',
                    success: function (data, status, xmlHttpReq) {
                        if (thisLink.data('view') == 'family') {
                            location.reload();
                        } else {
                            location.replace(window.CRM.root + "/");
                        }
                    }
                });
            }
        }
    });
});


$('.saveNoteAsWordFile').click(function (event) {
    var noteId = $(this).data("id");
    bootbox.confirm({
        title:i18next.t("Save your note"),
        message: i18next.t("Do you want to save your note as a Word File in your EDrive?"),
        buttons: {
            cancel: {
                className: 'btn-default',
                label: '<i class="fa fa-times"></i> ' + i18next.t("Cancel")
            },
            confirm: {
                className: 'btn-primary',
                label: '<i class="fa fa-floppy-o"></i> ' + i18next.t("Save")
            }
        },
        callback: function (result) {
            if(result) {
                window.CRM.APIRequest({
                  method: 'POST',
                  path: 'persons/saveNoteAsWordFile',
                  data: JSON.stringify({"personId":window.CRM.iPersonId,"noteId":noteId})
                }).done(function(data) {
                  // reload toolbar
                  if (window.CRM.dataEDriveTable != undefined) {
                     window.CRM.reloadEDriveTable();
                  }
                });
            }
        }
    });
});
